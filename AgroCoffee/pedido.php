<?php
/* ==========================================================================
 *  pedido.php
 *  ROTEADOR de Pedido.
 *
 *  Cliente:
 *      pedido.php?fun=cadastrar      -> CadastrarPedido (vem da página do produto)
 *      pedido.php?fun=meus           -> MeusPedidos
 *      pedido.php?fun=cancelar&id=1  -> CancelarPedido
 *  Vendedor / gerente:
 *      pedido.php?fun=listar         -> ListarPedido
 *      pedido.php?fun=novo           -> NovoPedido (WhatsApp / balcão)
 *      pedido.php?fun=exibir&id=1    -> ExibirPedido (concluir / cancelar)
 * ========================================================================== */

session_start();
require_once __DIR__ . "/controle/Acesso.php";

require_once __DIR__ . "/visao/layout/topo.php";

$fun = isset($_GET["fun"]) ? $_GET["fun"] : "";

switch ($fun) {
    case "cadastrar":
        require_once __DIR__ . "/controle/pedido/CadastrarPedido.php";
        new CadastrarPedido();
        break;

    case "meus":
        require_once __DIR__ . "/controle/pedido/MeusPedidos.php";
        new MeusPedidos();
        break;

    case "cancelar":
        require_once __DIR__ . "/controle/pedido/CancelarPedido.php";
        new CancelarPedido();
        break;

    case "novo":
        require_once __DIR__ . "/controle/pedido/NovoPedido.php";
        new NovoPedido();
        break;

    case "exibir":
        require_once __DIR__ . "/controle/pedido/ExibirPedido.php";
        new ExibirPedido();
        break;

    case "listar":
    default:
        require_once __DIR__ . "/controle/pedido/ListarPedido.php";
        new ListarPedido();
        break;
}

require_once __DIR__ . "/visao/layout/base.php";
