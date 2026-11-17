<?php
/* ==========================================================================
 *  ExibirCliente.php
 *  Camada: CONTROLE
 *
 *  Ficha do cliente com o histórico de pedidos dele (site e balcão).
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/cliente/ClienteDAO.php";
require_once __DIR__ . "/../../modelo/pedido/PedidoDAO.php";

class ExibirCliente {

    public function __construct() {

        if (!temAcesso(array("vendedor", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao     = new ClienteDAO();
        $cliente = isset($_GET["id"]) ? $dao->exibir($_GET["id"]) : null;

        if ($cliente === null) {
            echo "<main class='conteudo'><p class='erro'>Cliente não encontrado.</p></main>";
            return;
        }

        $pedidoDAO = new PedidoDAO();
        $pedidos   = $pedidoDAO->listarDoCliente($cliente->getId());
        require __DIR__ . "/../../visao/cliente/exibe.php";
    }
}
