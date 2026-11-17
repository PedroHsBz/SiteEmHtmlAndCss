<?php
/* ==========================================================================
 *  CancelarPedido.php
 *  Camada: CONTROLE
 *
 *  O cliente cancela um pedido seu que ainda está Pendente.
 *  Em 2 passos, igual à exclusão da agenda:
 *    1. pedido.php?fun=cancelar&id=5           -> pede confirmação
 *    2. pedido.php?fun=cancelar&id=5&conf=sim  -> cancela
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/pedido/PedidoDAO.php";
require_once __DIR__ . "/../../modelo/cliente/ClienteDAO.php";

class CancelarPedido {

    public function __construct() {

        if (!temAcesso(array("cliente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $clienteDAO = new ClienteDAO();
        $cliente    = $clienteDAO->buscarPorUsuario(usuarioLogado()->getId());
        $clienteId  = $cliente === null ? 0 : $cliente->getId();

        $dao    = new PedidoDAO();
        $pedido = $dao->exibir($_GET["id"]);

        // O cliente só pode cancelar pedido dele e que esteja pendente
        if ($pedido === null || $pedido->getClienteId() != $clienteId || $pedido->getStatus() != "Pendente") {
            $erro  = "Este pedido não pode ser cancelado.";
            $lista = $dao->listarDoCliente($clienteId);
            require __DIR__ . "/../../visao/pedido/meus.php";
            return;
        }

        if (isset($_GET["conf"]) && $_GET["conf"] == "sim") {
            $dao->cancelar($pedido->getId(), "Cancelado pelo cliente");
            $status = "Pedido #" . $pedido->getId() . " cancelado.";
            $lista  = $dao->listarDoCliente($clienteId);
            require __DIR__ . "/../../visao/pedido/meus.php";
            return;
        }

        require __DIR__ . "/../../visao/pedido/confirmaCancelamento.php";
    }
}
