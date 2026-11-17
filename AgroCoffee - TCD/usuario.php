<?php
/* ==========================================================================
 *  usuario.php
 *  ROTEADOR de Usuário.
 *
 *      usuario.php?fun=logar     -> LoginUsuario
 *      usuario.php?fun=cadastrar -> CadastrarUsuario (cliente se cadastra)
 *      usuario.php?fun=inicio    -> página inicial do usuário logado
 *      usuario.php?fun=listar    -> ListarUsuario   (gerente)
 *      usuario.php?fun=salvar    -> SalvarUsuario   (gerente)
 *      usuario.php?fun=ativar    -> AtivarUsuario   (gerente)
 *      usuario.php?fun=sair      -> sai do sistema
 * ========================================================================== */

session_start();
require_once __DIR__ . "/controle/Acesso.php";

$fun = isset($_GET["fun"]) ? $_GET["fun"] : "";

// O "sair" vem antes do topo para o menu já aparecer deslogado
if ($fun == "sair") {
    $_SESSION = array();
    session_destroy();
    header("Location: index.php");
    exit;
}

require_once __DIR__ . "/visao/layout/topo.php";

switch ($fun) {
    case "cadastrar":
        require_once __DIR__ . "/controle/usuario/CadastrarUsuario.php";
        new CadastrarUsuario();
        break;

    case "inicio":
        if (usuarioLogado() === null) {
            require_once __DIR__ . "/visao/layout/semAcesso.php";
        } else {
            require_once __DIR__ . "/visao/usuario/inicio.php";
        }
        break;

    case "listar":
        require_once __DIR__ . "/controle/usuario/ListarUsuario.php";
        new ListarUsuario();
        break;

    case "salvar":
        require_once __DIR__ . "/controle/usuario/SalvarUsuario.php";
        new SalvarUsuario();
        break;

    case "ativar":
        require_once __DIR__ . "/controle/usuario/AtivarUsuario.php";
        new AtivarUsuario();
        break;

    case "logar":
    default:
        require_once __DIR__ . "/controle/usuario/LoginUsuario.php";
        new LoginUsuario();
        break;
}

require_once __DIR__ . "/visao/layout/base.php";
