<?php
/* ==========================================================================
 *  pagina.php
 *  ROTEADOR das páginas institucionais.
 *
 *      pagina.php?fun=sobre     -> Sobre Nós
 *      pagina.php?fun=servicos  -> Serviços
 * ========================================================================== */

session_start();
require_once __DIR__ . "/controle/Acesso.php";

require_once __DIR__ . "/visao/layout/topo.php";

$fun = isset($_GET["fun"]) ? $_GET["fun"] : "";

switch ($fun) {
    case "sobre":
        require_once __DIR__ . "/visao/pagina/sobre.php";
        break;

    case "servicos":
        require_once __DIR__ . "/visao/pagina/servicos.php";
        break;

    default:
        require_once __DIR__ . "/visao/pagina/home.php";
        break;
}

require_once __DIR__ . "/visao/layout/base.php";
