<?php
/* ==========================================================================
 *  SalvarCliente.php
 *  Camada: CONTROLE
 *
 *  O vendedor (ou gerente) cadastra ou altera um cliente.
 *  O cliente cadastrado aqui NÃO tem login; se um dia ele criar conta no
 *  site com o mesmo CPF/CNPJ, a conta é ligada a este cadastro.
 *    cliente.php?fun=salvar        -> novo
 *    cliente.php?fun=salvar&id=3   -> alterar o cliente 3
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/cliente/ClienteDAO.php";
require_once __DIR__ . "/../Validacao.php";

class SalvarCliente {

    public function __construct() {

        if (!temAcesso(array("vendedor", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao = new ClienteDAO();

        if (!isset($_POST["enviar"])) {
            $c = isset($_GET["id"]) ? $dao->exibir($_GET["id"]) : new Cliente();
            require __DIR__ . "/../../visao/cliente/form.php";
            return;
        }

        $c = new Cliente();
        $c->setId($_POST["id"]);
        $c->setNome(trim($_POST["nome"]));
        $c->setTipo($_POST["tipo"] == "Jurídica" ? "Jurídica" : "Física");
        $c->setCpfCnpj(trim($_POST["cpf_cnpj"]));
        $c->setTelefone(trim($_POST["telefone"]));
        $c->setEmail(trim($_POST["email"]));

        if ($c->getNome() == "" || $c->getTelefone() == "") {
            $erro = "Informe o nome e o telefone.";
        } elseif (!documentoValido($c->getTipo(), $c->getCpfCnpj())) {
            $erro = erroDocumento($c->getTipo());
        } elseif ($dao->cpfCnpjEmUso($c->getCpfCnpj(), (int) $c->getId())) {
            $erro = "Já existe um cliente com este CPF/CNPJ.";
        }

        if (isset($erro)) {
            require __DIR__ . "/../../visao/cliente/form.php";
            return;
        }

        if ($c->getId() == "") {
            $dao->cadastrar($c);
            $status = "Cliente " . $c->getNome() . " cadastrado.";
        } else {
            $dao->alterar($c);
            $status = "Cliente " . $c->getNome() . " alterado.";
        }

        $busca = "";
        $lista = $dao->listar();
        require __DIR__ . "/../../visao/cliente/lista.php";
    }
}
