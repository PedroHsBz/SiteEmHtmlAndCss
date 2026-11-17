<?php
/* ==========================================================================
 *  PagarConta.php
 *  Camada: CONTROLE
 *
 *  Registra o pagamento (conta a pagar) ou recebimento (conta a receber).
 *  Se a conta for de um orçamento, o estoque recebe os produtos.
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/conta/ContaDAO.php";

class PagarConta {

    public function __construct() {

        if (!temAcesso(array("financeiro", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao   = new ContaDAO();
        $conta = isset($_POST["id"]) ? $dao->exibir($_POST["id"]) : null;

        $dataPagamento = isset($_POST["pago_em"]) ? $_POST["pago_em"] : "";

        if ($conta === null) {
            $erro = "Conta não encontrada.";
        } elseif ($dataPagamento == "") {
            $erro = "Informe o dia do pagamento.";
        } elseif ($dataPagamento > date("Y-m-d")) {
            $erro = "O dia do pagamento não pode ser no futuro.";
        } else {
            $erro = $dao->pagar($conta, $dataPagamento);
            if ($erro === null) {
                $dia = dataBr($dataPagamento);
                if ($conta->getOrcamentoId() != null) {
                    $status = "Conta paga em " . $dia . ". Os produtos do orçamento entraram no estoque.";
                } elseif ($conta->getTipo() == "Receber") {
                    $status = "Recebimento registrado em " . $dia . ".";
                    $juros  = $conta->calcularJuros($dataPagamento);
                    if ($juros > 0) {
                        $status .= " Juros de " . $conta->calcularMesesAtraso($dataPagamento) . " mês(es) de atraso: "
                                 . dinheiro($juros) . ". Total recebido: " . dinheiro($conta->getValor() + $juros) . ".";
                    }
                } else {
                    $status = "Pagamento registrado em " . $dia . ".";
                }
            }
        }

        $tipo   = "";
        $filtro = "";
        $lista  = $dao->listar("", "");
        require __DIR__ . "/../../visao/conta/lista.php";
    }
}
