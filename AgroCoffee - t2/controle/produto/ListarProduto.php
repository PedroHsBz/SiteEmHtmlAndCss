<?php
/* ==========================================================================
 *  ListarProduto.php
 *  Camada: CONTROLE
 *
 *  Catálogo do site: mostra só os produtos ativos.
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/produto/ProdutoDAO.php";

class ListarProduto {

    public function __construct() {
        $dao   = new ProdutoDAO();
        $busca = isset($_GET["busca"]) ? trim($_GET["busca"]) : "";
        $lista = $dao->listar(true, $busca);
        require __DIR__ . "/../../visao/produto/lista.php";
    }
}
