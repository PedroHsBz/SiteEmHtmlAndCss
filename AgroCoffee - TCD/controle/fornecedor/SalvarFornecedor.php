<?php
/* ==========================================================================
 *  SalvarFornecedor.php
 *  Camada: CONTROLE
 *
 *  Cadastra ou altera um fornecedor.
 *    fornecedor.php?fun=salvar        -> novo
 *    fornecedor.php?fun=salvar&id=3   -> alterar o fornecedor 3
 *  Fornecedor inativo não aparece na hora de registrar orçamento.
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/fornecedor/FornecedorDAO.php";

class SalvarFornecedor {

    public function __construct() {

        if (!temAcesso(array("vendedor", "financeiro", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao = new FornecedorDAO();

        if (!isset($_POST["enviar"])) {
            $f = isset($_GET["id"]) ? $dao->exibir($_GET["id"]) : new Fornecedor();
            require __DIR__ . "/../../visao/fornecedor/form.php";
            return;
        }

        $f = new Fornecedor();
        $f->setId($_POST["id"]);
        $f->setNome(trim($_POST["nome"]));
        $f->setCnpj(trim($_POST["cnpj"]));
        $f->setTelefone(trim($_POST["telefone"]));
        $f->setEmail(trim($_POST["email"]));
        $f->setAtivo(isset($_POST["ativo"]) ? 1 : 0);

        if ($f->getNome() == "" || $f->getTelefone() == "") {
            $erro = "Informe o nome e o telefone.";
        } elseif ($dao->cnpjEmUso($f->getCnpj(), (int) $f->getId())) {
            $erro = "Já existe um fornecedor com este CNPJ.";
        }

        if (isset($erro)) {
            require __DIR__ . "/../../visao/fornecedor/form.php";
            return;
        }

        if ($f->getId() == "") {
            $dao->cadastrar($f);
            $status = "Fornecedor " . $f->getNome() . " cadastrado.";
        } else {
            $dao->alterar($f);
            $status = "Fornecedor " . $f->getNome() . " alterado.";
        }

        $busca = "";
        $lista = $dao->listar(false);
        require __DIR__ . "/../../visao/fornecedor/lista.php";
    }
}
