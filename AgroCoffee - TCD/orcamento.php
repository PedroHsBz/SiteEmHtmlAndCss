<?php
/* ==========================================================================
 *  orcamento.php
 *  ROTEADOR de Orçamento de reposição.
 *
 *      orcamento.php?fun=listar         -> ListarOrcamento
 *      orcamento.php?fun=cadastrar      -> CadastrarOrcamento (vendedor)
 *      orcamento.php?fun=exibir&id=1    -> ExibirOrcamento (gerente aprova / rejeita)
 * ========================================================================== */

session_start();
require_once __DIR__ . "/controle/Acesso.php";

require_once __DIR__ . "/visao/layout/topo.php";

$fun = isset($_GET["fun"]) ? $_GET["fun"] : "listar";

switch ($fun) {
    case "cadastrar":
        require_once __DIR__ . "/controle/orcamento/CadastrarOrcamento.php";
        new CadastrarOrcamento();
        break;

    case "exibir":
        require_once __DIR__ . "/controle/orcamento/ExibirOrcamento.php";
        new ExibirOrcamento();
        break;

    case "listar":
    default:
        require_once __DIR__ . "/controle/orcamento/ListarOrcamento.php";
        new ListarOrcamento();
        break;
}

require_once __DIR__ . "/visao/layout/base.php";
