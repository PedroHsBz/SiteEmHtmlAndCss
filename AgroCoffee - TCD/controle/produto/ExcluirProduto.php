<?php
/* ==========================================================================
 *  ExcluirProduto.php
 *  Camada: CONTROLE
 *
 *  Exclusão em 2 passos, igual à agenda:
 *    1. produto.php?fun=excluir&id=3           -> pede confirmação
 *    2. produto.php?fun=excluir&id=3&conf=sim  -> exclui e volta para a lista
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/produto/ProdutoDAO.php";

class ExcluirProduto {

    public function __construct() {

        if (!temAcesso(array("vendedor", "financeiro", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao     = new ProdutoDAO();
        $produto = isset($_GET["id"]) ? $dao->exibir($_GET["id"]) : null;

        if ($produto === null) {
            $erro  = "Produto não encontrado.";
            $lista = $dao->listar(false);
            require __DIR__ . "/../../visao/produto/admin.php";
            return;
        }

        // Passo 2 — confirmado: exclui e mostra a lista
        if (isset($_GET["conf"]) && $_GET["conf"] == "sim") {
            $erro = $dao->excluir($produto->getId());

            if ($erro === null) {
                // Apaga a foto do disco se ela foi enviada pelo painel
                $arquivo = __DIR__ . "/../../" . $produto->getImagem();
                if (strpos($produto->getImagem(), "assets/img/produtos/") === 0 && file_exists($arquivo)) {
                    unlink($arquivo);
                }
                $status = "Produto " . $produto->getNome() . " excluído.";
            }

            $lista = $dao->listar(false);
            require __DIR__ . "/../../visao/produto/admin.php";
            return;
        }

        // Passo 1 — pede confirmação
        require __DIR__ . "/../../visao/produto/confirmaExclusao.php";
    }
}
