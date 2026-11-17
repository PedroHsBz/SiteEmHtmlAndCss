<?php
/* ==========================================================================
 *  OrcamentoDAO.php
 *  Camada: MODELO -> DAO
 *
 *  Operações no banco da tabela orcamento.
 *    aprovar()  -> status Aprovado + gera a conta a pagar
 *    rejeitar() -> status Rejeitado com o motivo
 *    concluir() -> dá entrada no estoque + status Concluído
 *                  (chamado pelo ContaDAO quando a conta é paga)
 * ========================================================================== */

require_once __DIR__ . "/../ConnectionFactory.php";
require_once __DIR__ . "/Orcamento.php";
require_once __DIR__ . "/../produto/ProdutoDAO.php";

class OrcamentoDAO {

    private $con;

    private $select = "SELECT o.*, f.nome AS fornecedor_nome,
                              CONCAT(pr.marca, ' ', pr.modelo) AS produto_nome
                         FROM orcamento o
                         JOIN fornecedor f ON f.id  = o.fornecedor_id
                         JOIN produto pr   ON pr.id = o.produto_id";

    public function __construct() {
        $this->con = ConnectionFactory::getConnection();
    }

    public function cadastrar(Orcamento $o) {
        try {
            $stmt = $this->con->prepare(
                "INSERT INTO orcamento (fornecedor_id, produto_id, quantidade, valor_unitario, vencimento)
                 VALUES (:fornecedor, :produto, :quantidade, :valor, :vencimento)"
            );
            $stmt->bindValue(":fornecedor", $o->getFornecedorId());
            $stmt->bindValue(":produto",    $o->getProdutoId());
            $stmt->bindValue(":quantidade", $o->getQuantidade());
            $stmt->bindValue(":valor",      $o->getValorUnitario());
            $stmt->bindValue(":vencimento", $o->getVencimento());
            return $stmt->execute();
        } catch (PDOException $ex) {
            echo "Erro ao cadastrar orçamento: " . $ex->getMessage();
            return false;
        }
    }

    // $busca: texto procurado no fornecedor ou no produto (vazio = todos)
    public function listar($busca = "") {
        try {
            if ($busca == "") {
                $stmt = $this->con->query($this->select . " ORDER BY o.id DESC");
            } else {
                $stmt = $this->con->prepare($this->select . " WHERE CONCAT(f.nome, ' ', pr.marca, ' ', pr.modelo) LIKE :busca ORDER BY o.id DESC");
                $stmt->bindValue(":busca", "%" . $busca . "%");
                $stmt->execute();
            }
            $lista = array();
            while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $lista[] = $this->montar($linha);
            }
            return $lista;
        } catch (PDOException $ex) {
            echo "Erro ao listar orçamentos: " . $ex->getMessage();
            return array();
        }
    }

    public function exibir($id) {
        try {
            $stmt = $this->con->prepare($this->select . " WHERE o.id = :id");
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $linha ? $this->montar($linha) : null;
        } catch (PDOException $ex) {
            echo "Erro ao exibir orçamento: " . $ex->getMessage();
            return null;
        }
    }

    // Devolve null se deu certo, ou a mensagem de erro.
    public function aprovar(Orcamento $o) {
        if ($o->getStatus() != "Pendente") {
            return "Só é possível aprovar orçamentos pendentes.";
        }

        try {
            $this->con->beginTransaction();

            $stmt = $this->con->prepare("UPDATE orcamento SET status = 'Aprovado' WHERE id = :id");
            $stmt->bindValue(":id", $o->getId());
            $stmt->execute();

            $stmt = $this->con->prepare(
                "INSERT INTO conta (tipo, descricao, valor, vencimento, orcamento_id)
                 VALUES ('Pagar', :descricao, :valor, :vencimento, :orcamento)"
            );
            $stmt->bindValue(":descricao",  "Orçamento #" . $o->getId() . " - " . $o->getFornecedorNome());
            $stmt->bindValue(":valor",      $o->getTotal());
            $stmt->bindValue(":vencimento", $o->getVencimento());
            $stmt->bindValue(":orcamento",  $o->getId());
            $stmt->execute();

            $this->con->commit();
            return null;
        } catch (PDOException $ex) {
            $this->con->rollBack();
            return "Erro ao aprovar orçamento: " . $ex->getMessage();
        }
    }

    public function rejeitar($id, $motivo) {
        try {
            $stmt = $this->con->prepare(
                "UPDATE orcamento SET status = 'Rejeitado', motivo_rejeicao = :motivo
                  WHERE id = :id AND status = 'Pendente'"
            );
            $stmt->bindValue(":motivo", $motivo);
            $stmt->bindValue(":id",     $id);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $ex) {
            echo "Erro ao rejeitar orçamento: " . $ex->getMessage();
            return false;
        }
    }

    // Chamado dentro da transação do ContaDAO::pagar()
    public function concluir($id) {
        $o = $this->exibir($id);
        if ($o === null || $o->getStatus() != "Aprovado") {
            return;
        }

        $produtoDAO = new ProdutoDAO();
        $produtoDAO->movimentar($o->getProdutoId(), "Entrada", $o->getQuantidade(), "Reposição do orçamento #" . $o->getId());

        $stmt = $this->con->prepare("UPDATE orcamento SET status = 'Concluído' WHERE id = :id");
        $stmt->bindValue(":id", $id);
        $stmt->execute();
    }

    private function montar($linha) {
        $o = new Orcamento();
        $o->setId($linha["id"]);
        $o->setFornecedorId($linha["fornecedor_id"]);
        $o->setFornecedorNome($linha["fornecedor_nome"]);
        $o->setProdutoId($linha["produto_id"]);
        $o->setProdutoNome($linha["produto_nome"]);
        $o->setQuantidade($linha["quantidade"]);
        $o->setValorUnitario($linha["valor_unitario"]);
        $o->setVencimento($linha["vencimento"]);
        $o->setStatus($linha["status"]);
        $o->setMotivoRejeicao($linha["motivo_rejeicao"]);
        $o->setData($linha["data_criacao"]);
        return $o;
    }
}
