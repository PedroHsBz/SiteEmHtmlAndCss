<?php
/* ==========================================================================
 *  GerenciarProduto.php
 *  Camada: CONTROLE
 *
 *  Lista de todos os produtos (ativos e ocultos) para vendedor,
 *  financeiro e gerente, com links para alterar e mexer no estoque.
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/produto/ProdutoDAO.php";

class GerenciarProduto {

    public function __construct() {

        if (!temAcesso(array("vendedor", "financeiro", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao   = new ProdutoDAO();
        $busca = isset($_GET["busca"]) ? trim($_GET["busca"]) : "";
        $campo = isset($_GET["campo"]) ? $_GET["campo"] : "";   // "Buscar em" (vazio = todos os campos)
        $lista = $dao->listar(false, $busca, $campo);
        require __DIR__ . "/../../visao/produto/admin.php";
    }
}
