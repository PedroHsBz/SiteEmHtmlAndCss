<?php
/* ==========================================================================
 *  ListarOrcamento.php
 *  Camada: CONTROLE
 *
 *  Lista de orçamentos de reposição (vendedor, financeiro e gerente).
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/orcamento/OrcamentoDAO.php";

class ListarOrcamento {

    public function __construct() {

        if (!temAcesso(array("vendedor", "financeiro", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao   = new OrcamentoDAO();
        $busca = isset($_GET["busca"]) ? trim($_GET["busca"]) : "";
        $lista = $dao->listar($busca);
        require __DIR__ . "/../../visao/orcamento/lista.php";
    }
}
