<?php
/* ==========================================================================
 *  fornecedor.php
 *  ROTEADOR de Fornecedor.
 *
 *      fornecedor.php?fun=listar   -> ListarFornecedor
 *      fornecedor.php?fun=salvar   -> SalvarFornecedor (novo / alterar)
 * ========================================================================== */

session_start();
require_once __DIR__ . "/controle/Acesso.php";

require_once __DIR__ . "/visao/layout/topo.php";

$fun = isset($_GET["fun"]) ? $_GET["fun"] : "listar";

switch ($fun) {
    case "salvar":
        require_once __DIR__ . "/controle/fornecedor/SalvarFornecedor.php";
        new SalvarFornecedor();
        break;

    case "listar":
    default:
        require_once __DIR__ . "/controle/fornecedor/ListarFornecedor.php";
        new ListarFornecedor();
        break;
}

require_once __DIR__ . "/visao/layout/base.php";
