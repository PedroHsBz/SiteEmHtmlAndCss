<?php
/* ==========================================================================
 *  ListarConta.php
 *  Camada: CONTROLE
 *
 *  Contas a pagar e a receber (financeiro e gerente), com filtros:
 *    conta.php?fun=listar&tipo=Pagar&status=Aberta
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/conta/ContaDAO.php";

class ListarConta {

    public function __construct() {

        if (!temAcesso(array("financeiro", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $tipo   = isset($_GET["tipo"])   ? $_GET["tipo"]   : "";
        $filtro = isset($_GET["status"]) ? $_GET["status"] : "";

        $dao   = new ContaDAO();
        $busca = isset($_GET["busca"]) ? trim($_GET["busca"]) : "";
        $lista = $dao->listar($tipo, $filtro, $busca);
        require __DIR__ . "/../../visao/conta/lista.php";
    }
}
