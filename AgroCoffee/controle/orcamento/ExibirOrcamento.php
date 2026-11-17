<?php
/* ==========================================================================
 *  ExibirOrcamento.php
 *  Camada: CONTROLE
 *
 *  Detalhe do orçamento. Só o GERENTE pode:
 *    - Aprovar: gera a conta a pagar (OrcamentoDAO::aprovar)
 *    - Rejeitar: precisa informar o motivo
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/orcamento/OrcamentoDAO.php";

class ExibirOrcamento {

    public function __construct() {

        if (!temAcesso(array("vendedor", "financeiro", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao       = new OrcamentoDAO();
        $id        = isset($_GET["id"]) ? $_GET["id"] : 0;
        $orcamento = $dao->exibir($id);
        $gerente   = temAcesso(array("gerente"));

        if ($orcamento === null) {
            echo "<main class='conteudo'><p class='erro'>Orçamento não encontrado.</p></main>";
            return;
        }

        if ($gerente && isset($_POST["aprovar"])) {
            $erro = $dao->aprovar($orcamento);
            if ($erro === null) {
                $status = "Orçamento aprovado. A conta a pagar foi enviada ao financeiro.";
            }
        }

        if ($gerente && isset($_POST["rejeitar"])) {
            if (trim($_POST["motivo"]) == "") {
                $erro = "Informe o motivo da rejeição.";
            } elseif ($dao->rejeitar($id, $_POST["motivo"])) {
                $status = "Orçamento rejeitado.";
            } else {
                $erro = "Só é possível rejeitar orçamentos pendentes.";
            }
        }

        $orcamento = $dao->exibir($id);
        require __DIR__ . "/../../visao/orcamento/detalhe.php";
    }
}
