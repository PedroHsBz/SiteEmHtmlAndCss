<?php
/* ==========================================================================
 *  EstoqueProduto.php
 *  Camada: CONTROLE
 *
 *  Mostra o estoque de um produto e suas movimentações, e permite dar
 *  entrada ou saída manual (ex.: correção de inventário).
 *    produto.php?fun=estoque&id=3
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/produto/ProdutoDAO.php";

class EstoqueProduto {

    public function __construct() {

        if (!temAcesso(array("vendedor", "financeiro", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao = new ProdutoDAO();
        $id  = isset($_GET["id"]) ? $_GET["id"] : 0;

        if (isset($_POST["enviar"])) {
            $quantidade = (int) $_POST["quantidade"];

            if ($quantidade < 1) {
                $erro = "A quantidade precisa ser pelo menos 1.";
            } elseif (trim($_POST["motivo"]) == "") {
                $erro = "Informe o motivo.";
            } elseif (!$dao->movimentar($id, $_POST["tipo"], $quantidade, $_POST["motivo"])) {
                $erro = "Estoque insuficiente para essa saída.";
            } else {
                $status = "Estoque atualizado.";
            }
        }

        $produto = $dao->exibir($id);
        if ($produto === null) {
            echo "<main class='conteudo'><p class='erro'>Produto não encontrado.</p></main>";
            return;
        }

        $movimentos = $dao->listarMovimentos($id);
        require __DIR__ . "/../../visao/produto/estoque.php";
    }
}
