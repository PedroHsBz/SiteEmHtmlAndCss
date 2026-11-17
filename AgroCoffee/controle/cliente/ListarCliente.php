<?php
/* ==========================================================================
 *  ListarCliente.php
 *  Camada: CONTROLE
 *
 *  Lista de clientes (vendedor e gerente), com busca:
 *    cliente.php?fun=listar&busca=jose
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/cliente/ClienteDAO.php";

class ListarCliente {

    public function __construct() {

        if (!temAcesso(array("vendedor", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao   = new ClienteDAO();
        $busca = isset($_GET["busca"]) ? trim($_GET["busca"]) : "";
        $campo = isset($_GET["campo"]) ? $_GET["campo"] : "";   // "Buscar em" (vazio = todos os campos)
        $lista = $dao->listar($busca, $campo);
        require __DIR__ . "/../../visao/cliente/lista.php";
    }
}
