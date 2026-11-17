<?php
/* ==========================================================================
 *  conta.php
 *  ROTEADOR de Contas a pagar e a receber.
 *
 *      conta.php?fun=listar     -> ListarConta
 *      conta.php?fun=cadastrar  -> CadastrarConta (conta avulsa)
 *      conta.php?fun=pagar      -> PagarConta (recebe o id por POST)
 *      conta.php?fun=relatorio  -> RelatorioConta (por período)
 * ========================================================================== */

session_start();
require_once __DIR__ . "/controle/Acesso.php";

require_once __DIR__ . "/visao/layout/topo.php";

$fun = isset($_GET["fun"]) ? $_GET["fun"] : "listar";

switch ($fun) {
    case "cadastrar":
        require_once __DIR__ . "/controle/conta/CadastrarConta.php";
        new CadastrarConta();
        break;

    case "pagar":
        require_once __DIR__ . "/controle/conta/PagarConta.php";
        new PagarConta();
        break;

    case "relatorio":
        require_once __DIR__ . "/controle/conta/RelatorioConta.php";
        new RelatorioConta();
        break;

    case "listar":
    default:
        require_once __DIR__ . "/controle/conta/ListarConta.php";
        new ListarConta();
        break;
}

require_once __DIR__ . "/visao/layout/base.php";
