<?php
/* ==========================================================================
 *  ListarPedido.php
 *  Camada: CONTROLE
 *
 *  Todos os pedidos, para vendedor e gerente.
 *    pedido.php?fun=listar&status=Pendente  -> filtra pelo status
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/pedido/PedidoDAO.php";

class ListarPedido {

    public function __construct() {

        if (!temAcesso(array("vendedor", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $filtro = isset($_GET["status"]) ? $_GET["status"] : "";

        $dao   = new PedidoDAO();
        $busca = isset($_GET["busca"]) ? trim($_GET["busca"]) : "";
        $campo = isset($_GET["campo"]) ? $_GET["campo"] : "";   // "Buscar em" (vazio = todos os campos)
        $lista = $dao->listar($filtro, $busca, $campo);
        require __DIR__ . "/../../visao/pedido/lista.php";
    }
}
