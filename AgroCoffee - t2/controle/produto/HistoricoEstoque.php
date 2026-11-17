<?php
/* ==========================================================================
 *  HistoricoEstoque.php
 *  Camada: CONTROLE
 *
 *  Histórico de movimentações de estoque de TODOS os produtos
 *  (vendas, reposições e ajustes manuais).
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/produto/ProdutoDAO.php";

class HistoricoEstoque {

    public function __construct() {

        if (!temAcesso(array("vendedor", "financeiro", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao        = new ProdutoDAO();
        $movimentos = $dao->listarTodosMovimentos();
        require __DIR__ . "/../../visao/produto/historico.php";
    }
}
