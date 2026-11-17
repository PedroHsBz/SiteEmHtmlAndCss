<?php
/* ==========================================================================
 *  LoginUsuario.php
 *  Camada: CONTROLE
 *
 *    - Formulário não enviado -> mostra o form de login.
 *    - Enviado -> confere e-mail e senha; se der certo, guarda o usuário
 *      na sessão e vai para a página inicial do usuário.
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/usuario/UsuarioDAO.php";

class LoginUsuario {

    public function __construct() {

        if (!isset($_POST["enviar"])) {
            require __DIR__ . "/../../visao/usuario/formLogin.php";
            return;
        }

        $dao     = new UsuarioDAO();
        $usuario = $dao->logar($_POST["email"], $_POST["senha"]);

        if ($usuario === null) {
            $erro = "E-mail ou senha incorretos.";
            require __DIR__ . "/../../visao/usuario/formLogin.php";
            return;
        }

        $_SESSION["usuario"] = serialize($usuario);

        // Recarrega a página para o menu já aparecer com o usuário logado
        echo "<script>location.href='usuario.php?fun=inicio';</script>";
    }
}
