<?php
/* ==========================================================================
 *  ExibirPedido.php
 *  Camada: CONTROLE
 *
 *  Detalhe do pedido para vendedor e gerente, com os botões:
 *    - Concluir: baixa o estoque e gera a conta a receber (PedidoDAO::concluir)
 *                e manda um e-mail para o cliente (Email::enviarPedidoConcluido)
 *    - Cancelar: precisa informar o motivo
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/pedido/PedidoDAO.php";
require_once __DIR__ . "/../../modelo/Email.php";

class ExibirPedido {

    public function __construct() {

        if (!temAcesso(array("vendedor", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao    = new PedidoDAO();
        $id     = isset($_GET["id"]) ? $_GET["id"] : 0;
        $pedido = $dao->exibir($id);

        if ($pedido === null) {
            echo "<main class='conteudo'><p class='erro'>Pedido não encontrado.</p></main>";
            return;
        }

        if (isset($_POST["concluir"])) {
            $parcelas = (int) $_POST["parcelas"];

            if ($_POST["vencimento"] == "") {
                $erro = "Informe o vencimento da 1ª parcela.";
            } elseif ($parcelas < 1 || $parcelas > 24) {
                $erro = "O número de parcelas deve ser de 1 a 24.";
            } else {
                $erro = $dao->concluir($pedido, $_POST["vencimento"], $parcelas);
                if ($erro === null) {
                    if ($parcelas == 1) {
                        $status = "Pedido concluído. Estoque baixado e conta a receber gerada (à vista).";
                    } else {
                        $status = "Pedido concluído. Estoque baixado e " . $parcelas . " contas a receber geradas, uma por parcela.";
                    }

                    // Avisa o cliente por e-mail (se der errado, a venda continua concluída)
                    $problemaEmail = Email::enviarPedidoConcluido($pedido);
                    if ($problemaEmail === null) {
                        $status .= " E-mail enviado para " . $pedido->getClienteEmail() . ".";
                    } else {
                        $status .= " E-mail não enviado: " . $problemaEmail . " Avise o cliente pelo WhatsApp.";
                    }
                }
            }
        }

        if (isset($_POST["cancelar"])) {
            if (trim($_POST["motivo"]) == "") {
                $erro = "Informe o motivo do cancelamento.";
            } elseif ($dao->cancelar($id, $_POST["motivo"])) {
                $status = "Pedido cancelado.";
            } else {
                $erro = "Só é possível cancelar pedidos pendentes.";
            }
        }

        $pedido = $dao->exibir($id);   // recarrega com o status novo
        require __DIR__ . "/../../visao/pedido/detalhe.php";
    }
}
