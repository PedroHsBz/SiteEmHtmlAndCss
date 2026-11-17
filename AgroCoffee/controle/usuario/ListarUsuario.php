<?php
/* ==========================================================================
 *  ListarUsuario.php
 *  Camada: CONTROLE
 *
 *  Lista de usuários (somente gerente).
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/usuario/UsuarioDAO.php";

class ListarUsuario {

    public function __construct() {

        if (!temAcesso(array("gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao   = new UsuarioDAO();
        $lista = $dao->listar();
        require __DIR__ . "/../../visao/usuario/lista.php";
    }
}
