<?php
/* ==========================================================================
 *  cliente.php
 *  ROTEADOR de Cliente (vendedor e gerente).
 *
 *      cliente.php?fun=listar        -> ListarCliente
 *      cliente.php?fun=salvar        -> SalvarCliente (novo / alterar)
 *      cliente.php?fun=exibir&id=1   -> ExibirCliente (ficha + pedidos)
 *      cliente.php?fun=excluir&id=1  -> ExcluirCliente
 * ========================================================================== */

session_start();
require_once __DIR__ . "/controle/Acesso.php";

require_once __DIR__ . "/visao/layout/topo.php";

$fun = isset($_GET["fun"]) ? $_GET["fun"] : "listar";

switch ($fun) {
    case "salvar":
        require_once __DIR__ . "/controle/cliente/SalvarCliente.php";
        new SalvarCliente();
        break;

    case "exibir":
        require_once __DIR__ . "/controle/cliente/ExibirCliente.php";
        new ExibirCliente();
        break;

    case "excluir":
        require_once __DIR__ . "/controle/cliente/ExcluirCliente.php";
        new ExcluirCliente();
        break;

    case "listar":
    default:
        require_once __DIR__ . "/controle/cliente/ListarCliente.php";
        new ListarCliente();
        break;
}

require_once __DIR__ . "/visao/layout/base.php";
