<?php
/* ==========================================================================
 *  index.php
 *  Página inicial do site.
 * ========================================================================== */

session_start();
require_once __DIR__ . "/controle/Acesso.php";

require_once __DIR__ . "/visao/layout/topo.php";
require_once __DIR__ . "/visao/pagina/home.php";
require_once __DIR__ . "/visao/layout/base.php";
