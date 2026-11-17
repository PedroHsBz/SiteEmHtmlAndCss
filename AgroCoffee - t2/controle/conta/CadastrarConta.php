<?php
/* ==========================================================================
 *  CadastrarConta.php
 *  Camada: CONTROLE
 *
 *  O financeiro cadastra uma conta avulsa (aluguel, energia, serviço...).
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/conta/ContaDAO.php";

class CadastrarConta {

    public function __construct() {

        if (!temAcesso(array("financeiro", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        if (!isset($_POST["enviar"])) {
            require __DIR__ . "/../../visao/conta/form.php";
            return;
        }

        $c = new Conta();
        $c->setTipo($_POST["tipo"]);
        $c->setDescricao($_POST["descricao"]);
        $c->setValor($_POST["valor"]);
        $c->setVencimento($_POST["vencimento"] == "" ? null : $_POST["vencimento"]);   // vazio = sem vencimento

        if (trim($c->getDescricao()) == "") {
            $erro = "Informe a descrição.";
        } elseif ($c->getValor() <= 0) {
            $erro = "Informe um valor maior que zero.";
        }

        if (isset($erro)) {
            require __DIR__ . "/../../visao/conta/form.php";
            return;
        }

        $dao = new ContaDAO();
        $dao->cadastrar($c);

        $status = "Conta cadastrada.";
        $tipo   = "";
        $filtro = "";
        $lista  = $dao->listar("", "");
        require __DIR__ . "/../../visao/conta/lista.php";
    }
}
