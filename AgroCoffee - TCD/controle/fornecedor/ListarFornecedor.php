<?php
/* ==========================================================================
 *  ListarFornecedor.php
 *  Camada: CONTROLE
 *
 *  Lista de fornecedores (vendedor, financeiro e gerente), com busca.
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/fornecedor/FornecedorDAO.php";

class ListarFornecedor {

    public function __construct() {

        if (!temAcesso(array("vendedor", "financeiro", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao   = new FornecedorDAO();
        $busca = isset($_GET["busca"]) ? trim($_GET["busca"]) : "";
        $lista = $dao->listar(false, $busca);
        require __DIR__ . "/../../visao/fornecedor/lista.php";
    }
}
