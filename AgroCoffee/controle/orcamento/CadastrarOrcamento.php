<?php
/* ==========================================================================
 *  CadastrarOrcamento.php
 *  Camada: CONTROLE
 *
 *  O vendedor registra um orçamento de reposição feito com o fornecedor.
 *  Ele fica Pendente até o gerente aprovar ou rejeitar.
 *    orcamento.php?fun=cadastrar&produto=3  -> já vem com o produto escolhido
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/orcamento/OrcamentoDAO.php";
require_once __DIR__ . "/../../modelo/produto/ProdutoDAO.php";
require_once __DIR__ . "/../../modelo/fornecedor/FornecedorDAO.php";

class CadastrarOrcamento {

    public function __construct() {

        if (!temAcesso(array("vendedor", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $produtoDAO    = new ProdutoDAO();
        $fornecedorDAO = new FornecedorDAO();
        $produtos      = $produtoDAO->listar(false);
        $fornecedores  = $fornecedorDAO->listar(true);   // só os ativos

        if (!isset($_POST["enviar"])) {
            require __DIR__ . "/../../visao/orcamento/form.php";
            return;
        }

        $o = new Orcamento();
        $o->setFornecedorId($_POST["fornecedor_id"]);
        $o->setProdutoId($_POST["produto_id"]);
        $o->setQuantidade((int) $_POST["quantidade"]);
        $o->setValorUnitario($_POST["valor_unitario"]);
        $o->setVencimento($_POST["vencimento"] == "" ? null : $_POST["vencimento"]);   // vazio = paga na chegada do produto

        if ($fornecedorDAO->exibir($o->getFornecedorId()) === null) {
            $erro = "Escolha o fornecedor.";
        } elseif ($produtoDAO->exibir($o->getProdutoId()) === null) {
            $erro = "Escolha um produto.";
        } elseif ($o->getQuantidade() < 1) {
            $erro = "A quantidade precisa ser pelo menos 1.";
        } elseif ($o->getValorUnitario() <= 0) {
            $erro = "Informe o valor unitário.";
        }

        if (isset($erro)) {
            require __DIR__ . "/../../visao/orcamento/form.php";
            return;
        }

        $dao = new OrcamentoDAO();
        $dao->cadastrar($o);

        $status = "Orçamento registrado. Aguardando aprovação do gerente.";
        $lista  = $dao->listar();
        require __DIR__ . "/../../visao/orcamento/lista.php";
    }
}
