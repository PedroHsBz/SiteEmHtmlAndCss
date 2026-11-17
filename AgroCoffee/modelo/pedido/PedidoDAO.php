<?php
/* ==========================================================================
 *  PedidoDAO.php
 *  Camada: MODELO -> DAO
 *
 *  Operações no banco da tabela pedido.
 *
 *  concluir() usa uma TRANSAÇÃO: baixa o estoque, muda o status e gera a
 *  conta a receber. Se qualquer passo falhar, nada é gravado (rollBack).
 * ========================================================================== */

require_once __DIR__ . "/../ConnectionFactory.php";
require_once __DIR__ . "/../Busca.php";
require_once __DIR__ . "/Pedido.php";
require_once __DIR__ . "/../produto/ProdutoDAO.php";

class PedidoDAO {

    private $con;

    // SELECT usado nas listagens, já trazendo o nome do cliente e do produto
    private $select = "SELECT p.*, c.nome AS cliente_nome, c.telefone AS cliente_telefone, c.email AS cliente_email,
                              CONCAT(pr.marca, ' ', pr.modelo) AS produto_nome
                         FROM pedido p
                         JOIN cliente c  ON c.id  = p.cliente_id
                         JOIN produto pr ON pr.id = p.produto_id";

    // Campos que podem ser escolhidos no "Buscar em" (ver modelo/Busca.php)
    public static function camposBusca() {
        return array(
            "numero"     => array("Nº do pedido",   "p.id"),
            "cliente"    => array("Cliente",        "c.nome"),
            "documento"  => array("CPF/CNPJ",       "c.cpf_cnpj"),
            "contato"    => array("Telefone/E-mail", "CONCAT_WS(' ', c.telefone, c.email)"),
            "produto"    => array("Produto",        "CONCAT_WS(' ', pr.marca, pr.modelo, pr.categoria)"),
            "quantidade" => array("Quantidade",     "p.quantidade"),
            "valor"      => array("Valor",          "p.valor"),
            "origem"     => array("Origem",         "p.origem"),
            "status"     => array("Status",         "CONCAT_WS(' ', p.status, IF(p.encomenda, 'Encomenda', NULL))"),
            "motivo"     => array("Motivo do cancelamento", "p.motivo_cancelamento"),
            "data"       => array("Data (dd/mm/aaaa)", "DATE_FORMAT(p.data_pedido, '%d/%m/%Y')"),
        );
    }

    public function __construct() {
        $this->con = ConnectionFactory::getConnection();
    }

    public function cadastrar(Pedido $p) {
        try {
            $stmt = $this->con->prepare(
                "INSERT INTO pedido (cliente_id, produto_id, quantidade, valor, origem, encomenda)
                 VALUES (:cliente, :produto, :quantidade, :valor, :origem, :encomenda)"
            );
            $stmt->bindValue(":cliente",    $p->getClienteId());
            $stmt->bindValue(":produto",    $p->getProdutoId());
            $stmt->bindValue(":quantidade", $p->getQuantidade());
            $stmt->bindValue(":valor",      $p->getValor());
            $stmt->bindValue(":origem",     $p->getOrigem());
            $stmt->bindValue(":encomenda",  $p->getEncomenda());
            return $stmt->execute();
        } catch (PDOException $ex) {
            echo "Erro ao cadastrar pedido: " . $ex->getMessage();
            return false;
        }
    }

    // Todos os pedidos (tela do vendedor).
    //   $status: vazio = todos
    //   $busca : texto procurado (vazio = todos)
    //   $campo : em qual campo procurar (vazio = em todos)
    public function listar($status, $busca = "", $campo = "") {
        try {
            $sql = $this->select . " WHERE 1 = 1";
            if ($status != "") {
                $sql .= " AND p.status = :status";
            }
            if ($busca != "") {
                $sql .= " AND " . sqlBusca(self::camposBusca(), $campo);
            }
            $sql .= " ORDER BY p.id DESC";

            $stmt = $this->con->prepare($sql);
            if ($status != "") {
                $stmt->bindValue(":status", $status);
            }
            if ($busca != "") {
                $stmt->bindValue(":busca", "%" . $busca . "%");
            }
            $stmt->execute();
            return $this->montarLista($stmt);
        } catch (PDOException $ex) {
            echo "Erro ao listar pedidos: " . $ex->getMessage();
            return array();
        }
    }

    // Pedidos de um cliente ("Meus pedidos" e ficha do cliente)
    public function listarDoCliente($clienteId) {
        try {
            $stmt = $this->con->prepare($this->select . " WHERE p.cliente_id = :id ORDER BY p.id DESC");
            $stmt->bindValue(":id", $clienteId);
            $stmt->execute();
            return $this->montarLista($stmt);
        } catch (PDOException $ex) {
            echo "Erro ao listar pedidos: " . $ex->getMessage();
            return array();
        }
    }

    public function exibir($id) {
        try {
            $stmt = $this->con->prepare($this->select . " WHERE p.id = :id");
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $linha ? $this->montar($linha) : null;
        } catch (PDOException $ex) {
            echo "Erro ao exibir pedido: " . $ex->getMessage();
            return null;
        }
    }

    // Só cancela pedido que ainda está Pendente
    public function cancelar($id, $motivo) {
        try {
            $stmt = $this->con->prepare(
                "UPDATE pedido SET status = 'Cancelado', motivo_cancelamento = :motivo
                  WHERE id = :id AND status = 'Pendente'"
            );
            $stmt->bindValue(":motivo", $motivo);
            $stmt->bindValue(":id",     $id);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $ex) {
            echo "Erro ao cancelar pedido: " . $ex->getMessage();
            return false;
        }
    }

    /* -------------------------------------------------------------------
     *  concluir() — devolve null se deu certo, ou a mensagem de erro.
     *    1. baixa o estoque do produto
     *    2. muda o pedido para Concluído
     *    3. gera uma conta a receber para cada parcela (1 = à vista),
     *       com um mês de intervalo a partir do $vencimento da 1ª
     * ------------------------------------------------------------------- */
    public function concluir(Pedido $p, $vencimento, $parcelas) {
        if ($p->getStatus() != "Pendente") {
            return "Só é possível concluir pedidos pendentes.";
        }

        try {
            $this->con->beginTransaction();

            $produtoDAO = new ProdutoDAO();
            $ok = $produtoDAO->movimentar($p->getProdutoId(), "Saída", $p->getQuantidade(), "Venda do pedido #" . $p->getId());
            if (!$ok) {
                $this->con->rollBack();
                return "Estoque insuficiente para concluir o pedido.";
            }

            $stmt = $this->con->prepare("UPDATE pedido SET status = 'Concluído' WHERE id = :id");
            $stmt->bindValue(":id", $p->getId());
            $stmt->execute();

            // Valor de cada parcela (arredondado para baixo); a diferença dos
            // centavos fica na última. Ex.: 100,00 em 3x = 33,33 + 33,33 + 33,34
            // (o round() interno evita erro de casas decimais, como 28,999999 virar 28)
            $valorParcela = floor(round($p->getValor() * 100 / $parcelas, 6)) / 100;

            $stmt = $this->con->prepare(
                "INSERT INTO conta (tipo, descricao, valor, vencimento, pedido_id, numero_parcela, total_parcelas)
                 VALUES ('Receber', :descricao, :valor, :vencimento, :pedido, :numero, :total)"
            );

            for ($i = 1; $i <= $parcelas; $i++) {
                if ($i == $parcelas) {
                    $valor = round($p->getValor() - $valorParcela * ($parcelas - 1), 2);
                } else {
                    $valor = $valorParcela;
                }

                // À vista: número e total ficam vazios (NULL)
                $descricao = "Venda do pedido #" . $p->getId() . " - " . $p->getClienteNome();
                $numero    = null;
                $total     = null;
                if ($parcelas > 1) {
                    $numero     = $i;
                    $total      = $parcelas;
                    $descricao .= " - parcela " . $i . "/" . $parcelas;
                }

                $stmt->bindValue(":descricao",  $descricao);
                $stmt->bindValue(":valor",      $valor);
                $stmt->bindValue(":vencimento", $this->somarMeses($vencimento, $i - 1));
                $stmt->bindValue(":pedido",     $p->getId());
                $stmt->bindValue(":numero",     $numero);
                $stmt->bindValue(":total",      $total);
                $stmt->execute();
            }

            $this->con->commit();
            return null;
        } catch (PDOException $ex) {
            $this->con->rollBack();
            return "Erro ao concluir pedido: " . $ex->getMessage();
        }
    }

    // Soma meses a uma data mantendo o dia. Se o mês não tiver esse dia
    // (ex.: 31 em fevereiro), usa o último dia do mês.
    private function somarMeses($data, $meses) {
        $dia = (int) date("d", strtotime($data));
        $mes = (int) date("m", strtotime($data)) + $meses;
        $ano = (int) date("Y", strtotime($data));

        $primeiroDia = mktime(0, 0, 0, $mes, 1, $ano);   // mktime ajusta mês 13 para janeiro do ano seguinte
        $ultimoDia   = (int) date("t", $primeiroDia);

        return date("Y-m-", $primeiroDia) . str_pad(min($dia, $ultimoDia), 2, "0", STR_PAD_LEFT);
    }

    private function montarLista($stmt) {
        $lista = array();
        while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lista[] = $this->montar($linha);
        }
        return $lista;
    }

    private function montar($linha) {
        $p = new Pedido();
        $p->setId($linha["id"]);
        $p->setClienteId($linha["cliente_id"]);
        $p->setClienteNome($linha["cliente_nome"]);
        $p->setClienteTelefone($linha["cliente_telefone"]);
        $p->setClienteEmail($linha["cliente_email"]);
        $p->setProdutoId($linha["produto_id"]);
        $p->setProdutoNome($linha["produto_nome"]);
        $p->setQuantidade($linha["quantidade"]);
        $p->setValor($linha["valor"]);
        $p->setOrigem($linha["origem"]);
        $p->setEncomenda($linha["encomenda"]);
        $p->setStatus($linha["status"]);
        $p->setMotivoCancelamento($linha["motivo_cancelamento"]);
        $p->setData($linha["data_pedido"]);
        return $p;
    }
}
