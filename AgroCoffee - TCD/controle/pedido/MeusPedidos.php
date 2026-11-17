<?php
/* ==========================================================================
 *  MeusPedidos.php
 *  Camada: CONTROLE
 *
 *  Pedidos do cliente logado (inclusive os feitos no balcão,
 *  se o cadastro dele foi ligado à conta do site).
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/pedido/PedidoDAO.php";
require_once __DIR__ . "/../../modelo/cliente/ClienteDAO.php";

class MeusPedidos {

    public function __construct() {

        if (!temAcesso(array("cliente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $clienteDAO = new ClienteDAO();
        $cliente    = $clienteDAO->buscarPorUsuario(usuarioLogado()->getId());

        $dao   = new PedidoDAO();
        $lista = $cliente === null ? array() : $dao->listarDoCliente($cliente->getId());
        require __DIR__ . "/../../visao/pedido/meus.php";
    }
}
