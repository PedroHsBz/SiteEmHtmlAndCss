<?php
/* ==========================================================================
 *  ContaDAO.php
 *  Camada: MODELO -> DAO
 *
 *  Operações no banco da tabela conta.
 *  pagar() usa transação: se a conta veio de um orçamento, o pagamento
 *  também dá entrada no estoque e conclui o orçamento.
 * ========================================================================== */

require_once __DIR__ . "/../ConnectionFactory.php";
require_once __DIR__ . "/Conta.php";
require_once __DIR__ . "/../orcamento/OrcamentoDAO.php";

class ContaDAO {

    private $con;

    public function __construct() {
        $this->con = ConnectionFactory::getConnection();
    }

    // Conta avulsa (aluguel, energia...) cadastrada pelo financeiro
    public function cadastrar(Conta $c) {
        try {
            $stmt = $this->con->prepare(
                "INSERT INTO conta (tipo, descricao, valor, vencimento)
                 VALUES (:tipo, :descricao, :valor, :vencimento)"
            );
            $stmt->bindValue(":tipo",       $c->getTipo());
            $stmt->bindValue(":descricao",  $c->getDescricao());
            $stmt->bindValue(":valor",      $c->getValor());
            $stmt->bindValue(":vencimento", $c->getVencimento());
            return $stmt->execute();
        } catch (PDOException $ex) {
            echo "Erro ao cadastrar conta: " . $ex->getMessage();
            return false;
        }
    }

    /* -------------------------------------------------------------------
     *  listar() com filtros (vazio = sem filtro)
     *    $tipo  : Pagar | Receber
     *    $status: Aberta | Paga
     *    $busca : texto procurado na descrição
     * ------------------------------------------------------------------- */
    public function listar($tipo, $status, $busca = "") {
        try {
            $sql = "SELECT * FROM conta WHERE 1 = 1";
            if ($tipo != "") {
                $sql .= " AND tipo = :tipo";
            }
            if ($status != "") {
                $sql .= " AND status = :status";
            }
            if ($busca != "") {
                $sql .= " AND descricao LIKE :busca";
            }
            // Contas sem vencimento (pagas na entrega) ficam no fim da lista
            $sql .= " ORDER BY status, vencimento IS NULL, vencimento";

            $stmt = $this->con->prepare($sql);
            if ($tipo != "") {
                $stmt->bindValue(":tipo", $tipo);
            }
            if ($status != "") {
                $stmt->bindValue(":status", $status);
            }
            if ($busca != "") {
                $stmt->bindValue(":busca", "%" . $busca . "%");
            }
            $stmt->execute();

            $lista = array();
            while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $lista[] = $this->montar($linha);
            }
            return $lista;
        } catch (PDOException $ex) {
            echo "Erro ao listar contas: " . $ex->getMessage();
            return array();
        }
    }

    public function exibir($id) {
        try {
            $stmt = $this->con->prepare("SELECT * FROM conta WHERE id = :id");
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $linha ? $this->montar($linha) : null;
        } catch (PDOException $ex) {
            echo "Erro ao exibir conta: " . $ex->getMessage();
            return null;
        }
    }

    // Registra o pagamento (ou recebimento) no dia informado.
    // Devolve null se deu certo, ou a mensagem de erro.
    public function pagar(Conta $c, $dataPagamento) {
        if ($c->getStatus() != "Aberta") {
            return "Esta conta já foi paga.";
        }

        try {
            $this->con->beginTransaction();

            // Juros do atraso até o dia do pagamento (zero se pagou em dia ou se é conta a pagar)
            $stmt = $this->con->prepare("UPDATE conta SET status = 'Paga', pago_em = :data, juros = :juros WHERE id = :id");
            $stmt->bindValue(":data",  $dataPagamento);
            $stmt->bindValue(":juros", $c->calcularJuros($dataPagamento));
            $stmt->bindValue(":id",    $c->getId());
            $stmt->execute();

            if ($c->getOrcamentoId() != null) {
                $orcamentoDAO = new OrcamentoDAO();
                $orcamentoDAO->concluir($c->getOrcamentoId());
            }

            $this->con->commit();
            return null;
        } catch (PDOException $ex) {
            $this->con->rollBack();
            return "Erro ao pagar conta: " . $ex->getMessage();
        }
    }

    /* -------------------------------------------------------------------
     *  relatorio() — contas do período: as que vencem no período ou que
     *  foram pagas/recebidas nele. As contas sem vencimento ainda em
     *  aberto também entram, porque podem ser pagas a qualquer momento.
     * ------------------------------------------------------------------- */
    public function relatorio($de, $ate) {
        try {
            $stmt = $this->con->prepare(
                "SELECT * FROM conta
                  WHERE vencimento BETWEEN :de1 AND :ate1
                     OR pago_em    BETWEEN :de2 AND :ate2
                     OR (vencimento IS NULL AND status = 'Aberta')
                  ORDER BY tipo DESC, vencimento IS NULL, vencimento"
            );
            $stmt->bindValue(":de1",  $de);
            $stmt->bindValue(":ate1", $ate);
            $stmt->bindValue(":de2",  $de);
            $stmt->bindValue(":ate2", $ate);
            $stmt->execute();

            $lista = array();
            while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $lista[] = $this->montar($linha);
            }
            return $lista;
        } catch (PDOException $ex) {
            echo "Erro ao gerar relatório: " . $ex->getMessage();
            return array();
        }
    }

    private function montar($linha) {
        $c = new Conta();
        $c->setId($linha["id"]);
        $c->setTipo($linha["tipo"]);
        $c->setDescricao($linha["descricao"]);
        $c->setValor($linha["valor"]);
        $c->setVencimento($linha["vencimento"]);
        $c->setStatus($linha["status"]);
        $c->setPagoEm($linha["pago_em"]);
        $c->setJuros($linha["juros"]);
        $c->setPedidoId($linha["pedido_id"]);
        $c->setNumeroParcela($linha["numero_parcela"]);
        $c->setTotalParcelas($linha["total_parcelas"]);
        $c->setOrcamentoId($linha["orcamento_id"]);
        return $c;
    }
}
