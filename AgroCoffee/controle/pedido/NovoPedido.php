<?php
/* ==========================================================================
 *  NovoPedido.php
 *  Camada: CONTROLE
 *
 *  O vendedor registra um pedido que chegou pelo WhatsApp ou no balcão.
 *  Ele escolhe um cliente da lista OU cadastra um cliente novo na hora
 *  (nome e telefone, CPF/CNPJ opcional), sem precisar criar login.
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/pedido/PedidoDAO.php";
require_once __DIR__ . "/../../modelo/produto/ProdutoDAO.php";
require_once __DIR__ . "/../../modelo/cliente/ClienteDAO.php";
require_once __DIR__ . "/../Validacao.php";

class NovoPedido {

    public function __construct() {

        if (!temAcesso(array("vendedor", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $produtoDAO = new ProdutoDAO();
        $clienteDAO = new ClienteDAO();
        $produtos   = $produtoDAO->listar(true);   // para os <select> do formulário
        $clientes   = $clienteDAO->listar();

        if (!isset($_POST["enviar"])) {
            require __DIR__ . "/../../visao/pedido/form.php";
            return;
        }

        $produto    = $produtoDAO->exibir($_POST["produto_id"]);
        $quantidade = (int) $_POST["quantidade"];
        $novoNome   = trim($_POST["novo_nome"]);
        $novoCpfCnpj = trim($_POST["novo_cpf_cnpj"]);
        $novoTipo    = $_POST["novo_tipo"] == "Jurídica" ? "Jurídica" : "Física";

        // Cliente: o escolhido na lista ou um novo
        $clienteId = $_POST["cliente_id"];

        if ($clienteId == "" && $novoNome == "") {
            $erro = "Escolha um cliente da lista ou cadastre um novo.";
        } elseif ($clienteId == "" && trim($_POST["novo_telefone"]) == "") {
            $erro = "Informe o telefone do cliente novo.";
        } elseif ($clienteId == "" && !documentoValido($novoTipo, $novoCpfCnpj)) {
            $erro = erroDocumento($novoTipo);
        } elseif ($clienteId == "" && $clienteDAO->cpfCnpjEmUso($novoCpfCnpj)) {
            $erro = "Já existe um cliente com este CPF/CNPJ. Escolha ele na lista.";
        } elseif ($produto === null) {
            $erro = "Escolha um produto.";
        } elseif ($quantidade < 1) {
            $erro = "A quantidade precisa ser pelo menos 1.";
        }

        if (isset($erro)) {
            require __DIR__ . "/../../visao/pedido/form.php";
            return;
        }

        if ($clienteId == "") {
            $c = new Cliente();
            $c->setNome($novoNome);
            $c->setTipo($novoTipo);
            $c->setCpfCnpj($novoCpfCnpj);
            $c->setTelefone(trim($_POST["novo_telefone"]));
            $c->setEmail("");
            $clienteId = $clienteDAO->cadastrar($c);
        }

        $p = new Pedido();
        $p->setClienteId($clienteId);
        $p->setProdutoId($produto->getId());
        $p->setQuantidade($quantidade);
        $p->setValor($produto->getPreco() * $quantidade);
        $p->setOrigem($_POST["origem"]);
        $p->setEncomenda($quantidade > $produto->getEstoque() ? 1 : 0);

        $dao = new PedidoDAO();
        $dao->cadastrar($p);

        $status = $p->getEncomenda() == 1 ? "Encomenda registrada. Registre o orçamento de reposição do produto." : "Pedido registrado.";
        $filtro = "";
        $busca  = "";
        $lista  = $dao->listar("");
        require __DIR__ . "/../../visao/pedido/lista.php";
    }
}
