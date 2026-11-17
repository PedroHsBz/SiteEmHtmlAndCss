<?php
/* ==========================================================================
 *  ProdutoDAO.php
 *  Camada: MODELO -> DAO
 *
 *  Operações no banco das tabelas produto e estoque_movimento.
 *  Toda mudança de estoque passa por movimentar(), que atualiza o
 *  estoque do produto e registra a movimentação.
 * ========================================================================== */

require_once __DIR__ . "/../ConnectionFactory.php";
require_once __DIR__ . "/../Busca.php";
require_once __DIR__ . "/Produto.php";

class ProdutoDAO {

    private $con;

    public function __construct() {
        $this->con = ConnectionFactory::getConnection();
    }

    // $somenteAtivos = true para o catálogo do site
    // Campos que podem ser escolhidos no "Buscar em" (ver modelo/Busca.php)
    public static function camposBusca() {
        return array(
            "id"        => array("Código",    "id"),
            "categoria" => array("Categoria", "categoria"),
            "marca"     => array("Marca",     "marca"),
            "modelo"    => array("Modelo",    "modelo"),
            "condicao"  => array("Condição",  "condicao"),
            "ano"       => array("Ano",       "ano"),
            "horas"     => array("Horas",     "horas"),
            "preco"     => array("Preço",     "preco"),
            "estoque"   => array("Estoque",   "estoque"),
            "descricao" => array("Descrição", "descricao"),
        );
    }

    // $busca: texto procurado (vazio = todos)
    // $campo: em qual campo procurar (vazio = em todos)
    public function listar($somenteAtivos, $busca = "", $campo = "") {
        try {
            $sql = "SELECT * FROM produto WHERE 1 = 1";
            if ($somenteAtivos) {
                $sql .= " AND ativo = 1";
            }
            if ($busca != "") {
                $sql .= " AND " . sqlBusca(self::camposBusca(), $campo);
            }
            $sql .= " ORDER BY categoria DESC, marca, modelo";

            $stmt = $this->con->prepare($sql);
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
            echo "Erro ao listar produtos: " . $ex->getMessage();
            return array();
        }
    }

    public function exibir($id) {
        try {
            $stmt = $this->con->prepare("SELECT * FROM produto WHERE id = :id");
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $linha ? $this->montar($linha) : null;
        } catch (PDOException $ex) {
            echo "Erro ao exibir produto: " . $ex->getMessage();
            return null;
        }
    }

    // O estoque começa em 0; a entrada é feita na tela de estoque.
    public function cadastrar(Produto $p) {
        try {
            $stmt = $this->con->prepare(
                "INSERT INTO produto (categoria, marca, modelo, condicao, ano, horas, preco, imagem, descricao, ativo)
                 VALUES (:categoria, :marca, :modelo, :condicao, :ano, :horas, :preco, :imagem, :descricao, :ativo)"
            );
            $this->bindDados($stmt, $p);
            return $stmt->execute();
        } catch (PDOException $ex) {
            echo "Erro ao cadastrar produto: " . $ex->getMessage();
            return false;
        }
    }

    public function alterar(Produto $p) {
        try {
            $stmt = $this->con->prepare(
                "UPDATE produto SET categoria = :categoria, marca = :marca, modelo = :modelo,
                        condicao = :condicao, ano = :ano, horas = :horas, preco = :preco,
                        imagem = :imagem, descricao = :descricao, ativo = :ativo
                  WHERE id = :id"
            );
            $this->bindDados($stmt, $p);
            $stmt->bindValue(":id", $p->getId());
            return $stmt->execute();
        } catch (PDOException $ex) {
            echo "Erro ao alterar produto: " . $ex->getMessage();
            return false;
        }
    }

    /* -------------------------------------------------------------------
     *  excluir() — devolve null se excluiu, ou a mensagem de erro.
     *  Produto que já tem pedido ou orçamento não pode ser excluído
     *  (perderia o histórico de vendas); nesse caso o certo é ocultar.
     * ------------------------------------------------------------------- */
    public function excluir($id) {
        try {
            $stmt = $this->con->prepare(
                "SELECT (SELECT COUNT(*) FROM pedido    WHERE produto_id = :id1)
                      + (SELECT COUNT(*) FROM orcamento WHERE produto_id = :id2) AS total"
            );
            $stmt->bindValue(":id1", $id);
            $stmt->bindValue(":id2", $id);
            $stmt->execute();
            $linha = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($linha["total"] > 0) {
                return "Este produto já tem pedidos ou orçamentos e não pode ser excluído. Desmarque \"Mostrar no site\" para ocultá-lo.";
            }

            $this->con->beginTransaction();

            $stmt = $this->con->prepare("DELETE FROM estoque_movimento WHERE produto_id = :id");
            $stmt->bindValue(":id", $id);
            $stmt->execute();

            $stmt = $this->con->prepare("DELETE FROM produto WHERE id = :id");
            $stmt->bindValue(":id", $id);
            $stmt->execute();

            $this->con->commit();
            return null;
        } catch (PDOException $ex) {
            if ($this->con->inTransaction()) {
                $this->con->rollBack();
            }
            return "Erro ao excluir produto: " . $ex->getMessage();
        }
    }

    /* -------------------------------------------------------------------
     *  movimentar() — dá entrada ou saída no estoque.
     *    $tipo: "Entrada" ou "Saída"
     *  Devolve false se for saída e não houver estoque suficiente.
     * ------------------------------------------------------------------- */
    public function movimentar($produtoId, $tipo, $quantidade, $motivo) {
        $produto = $this->exibir($produtoId);
        if ($produto === null) {
            return false;
        }

        if ($tipo == "Entrada") {
            $saldo = $produto->getEstoque() + $quantidade;
        } else {
            $saldo = $produto->getEstoque() - $quantidade;
        }
        if ($saldo < 0) {
            return false;
        }

        $stmt = $this->con->prepare("UPDATE produto SET estoque = :saldo WHERE id = :id");
        $stmt->bindValue(":saldo", $saldo);
        $stmt->bindValue(":id",    $produtoId);
        $stmt->execute();

        $stmt = $this->con->prepare(
            "INSERT INTO estoque_movimento (produto_id, tipo, quantidade, saldo, motivo)
             VALUES (:produto, :tipo, :quantidade, :saldo, :motivo)"
        );
        $stmt->bindValue(":produto",    $produtoId);
        $stmt->bindValue(":tipo",       $tipo);
        $stmt->bindValue(":quantidade", $quantidade);
        $stmt->bindValue(":saldo",      $saldo);
        $stmt->bindValue(":motivo",     $motivo);
        $stmt->execute();

        return true;
    }

    // Histórico de movimentações de um produto (array de arrays)
    public function listarMovimentos($produtoId) {
        $stmt = $this->con->prepare(
            "SELECT * FROM estoque_movimento WHERE produto_id = :id ORDER BY data DESC, id DESC"
        );
        $stmt->bindValue(":id", $produtoId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Histórico geral: movimentações de todos os produtos, já com o nome do produto
    public function listarTodosMovimentos() {
        $stmt = $this->con->query(
            "SELECT m.*, CONCAT(p.marca, ' ', p.modelo) AS produto_nome
               FROM estoque_movimento m
               JOIN produto p ON p.id = m.produto_id
              ORDER BY m.data DESC, m.id DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function bindDados($stmt, Produto $p) {
        $stmt->bindValue(":categoria", $p->getCategoria());
        $stmt->bindValue(":marca",     $p->getMarca());
        $stmt->bindValue(":modelo",    $p->getModelo());
        $stmt->bindValue(":condicao",  $p->getCondicao());
        $stmt->bindValue(":ano",       $p->getAno());
        $stmt->bindValue(":horas",     $p->getHoras());
        $stmt->bindValue(":preco",     $p->getPreco());
        $stmt->bindValue(":imagem",    $p->getImagem());
        $stmt->bindValue(":descricao", $p->getDescricao());
        $stmt->bindValue(":ativo",     $p->getAtivo());
    }

    private function montar($linha) {
        $p = new Produto();
        $p->setId($linha["id"]);
        $p->setCategoria($linha["categoria"]);
        $p->setMarca($linha["marca"]);
        $p->setModelo($linha["modelo"]);
        $p->setCondicao($linha["condicao"]);
        $p->setAno($linha["ano"]);
        $p->setHoras($linha["horas"]);
        $p->setPreco($linha["preco"]);
        $p->setImagem($linha["imagem"]);
        $p->setDescricao($linha["descricao"]);
        $p->setEstoque($linha["estoque"]);
        $p->setAtivo($linha["ativo"]);
        return $p;
    }
}
