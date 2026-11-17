<?php
/* ==========================================================================
 *  AtivarUsuario.php
 *  Camada: CONTROLE
 *
 *  O gerente ativa ou desativa um usuário (usuário desativado não entra).
 *    usuario.php?fun=ativar&id=3&ativo=0  -> desativa
 *    usuario.php?fun=ativar&id=3&ativo=1  -> ativa
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/usuario/UsuarioDAO.php";

class AtivarUsuario {

    public function __construct() {

        if (!temAcesso(array("gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao   = new UsuarioDAO();
        $ativo = $_GET["ativo"] == "1" ? 1 : 0;

        if ($_GET["id"] == usuarioLogado()->getId()) {
            $erro = "Você não pode desativar o seu próprio usuário.";
        } else {
            $dao->alterarAtivo($_GET["id"], $ativo);
            $status = $ativo == 1 ? "Usuário ativado." : "Usuário desativado.";
        }

        $lista = $dao->listar();
        require __DIR__ . "/../../visao/usuario/lista.php";
    }
}
