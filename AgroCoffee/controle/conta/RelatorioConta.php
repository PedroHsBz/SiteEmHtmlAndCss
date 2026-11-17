<?php
/* ==========================================================================
 *  RelatorioConta.php
 *  Camada: CONTROLE
 *
 *  Relatório financeiro de um período (financeiro e gerente):
 *    conta.php?fun=relatorio&de=2026-10-01&ate=2026-10-31
 *  Sem datas, usa o mês atual. Mostra o que entrou, o que saiu, o que
 *  ainda está em aberto (com juros) e o saldo. Pode ser impresso ou
 *  salvo em PDF pelo navegador.
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/conta/ContaDAO.php";

class RelatorioConta {

    public function __construct() {

        if (!temAcesso(array("financeiro", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $de   = isset($_GET["de"])  && $_GET["de"]  != "" ? $_GET["de"]  : date("Y-m-01");
        $ate  = isset($_GET["ate"]) && $_GET["ate"] != "" ? $_GET["ate"] : date("Y-m-t");
        $hoje = date("Y-m-d");

        if ($de > $ate) {
            $erro = "A data inicial não pode ser depois da data final.";
            $lista = array();
        } else {
            $dao   = new ContaDAO();
            $lista = $dao->relatorio($de, $ate);
        }

        /* ----------- Totais ----------- */
        $recebido       = 0;   // valor + juros das contas a receber recebidas no período
        $jurosRecebidos = 0;
        $pago           = 0;   // contas a pagar pagas no período
        $aReceber       = 0;   // contas a receber em aberto (com juros até hoje)
        $jurosAReceber  = 0;
        $aPagar         = 0;   // contas a pagar em aberto
        $vencidas       = 0;   // quantidade de contas em aberto já vencidas

        foreach ($lista as $c) {
            $pagaNoPeriodo = $c->getStatus() == "Paga" && $c->getPagoEm() >= $de && $c->getPagoEm() <= $ate;

            if ($c->getTipo() == "Receber") {
                if ($pagaNoPeriodo) {
                    $recebido       += $c->getValor() + $c->getJuros();
                    $jurosRecebidos += $c->getJuros();
                } elseif ($c->getStatus() == "Aberta") {
                    $juros          = $c->calcularJuros($hoje);
                    $aReceber      += $c->getValor() + $juros;
                    $jurosAReceber += $juros;
                }
            } else {
                if ($pagaNoPeriodo) {
                    $pago += $c->getValor();
                } elseif ($c->getStatus() == "Aberta") {
                    $aPagar += $c->getValor();
                }
            }

            if ($c->getStatus() == "Aberta" && $c->getVencimento() != null && $c->getVencimento() < $hoje) {
                $vencidas++;
            }
        }

        $saldo         = $recebido - $pago;                  // o que de fato entrou menos o que saiu
        $saldoPrevisto = $saldo + $aReceber - $aPagar;       // se tudo em aberto for quitado

        require __DIR__ . "/../../visao/conta/relatorio.php";
    }
}
