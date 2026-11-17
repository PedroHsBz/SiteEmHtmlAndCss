<?php
/* ==========================================================================
 *  CadastrarPedido.php
 *  Camada: CONTROLE
 *
 *  O cliente logado faz o pedido pela página do produto.
 *  O valor é calculado aqui: preço do produto x quantidade.
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/pedido/PedidoDAO.php";
require_once __DIR__ . "/../../modelo/produto/ProdutoDAO.php";
require_once __DIR__ . "/../../modelo/cliente/ClienteDAO.php";

class CadastrarPedido {

    public function __construct() {

        if (!temAcesso(array("cliente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $produtoDAO = new ProdutoDAO();
        $produto    = $produtoDAO->exibir($_POST["produto_id"]);
        $quantidade = (int) $_POST["quantidade"];

        if ($produto === null || $produto->getAtivo() == 0) {
            echo "<main class='conteudo'><p class='erro'>Produto não encontrado.</p></main>";
            return;
        }
        if ($quantidade < 1) {
            $erro = "A quantidade precisa ser pelo menos 1.";
            require __DIR__ . "/../../visao/produto/exibe.php";
            return;
        }

        // Cliente ligado ao usuário logado
        $clienteDAO = new ClienteDAO();
        $cliente    = $clienteDAO->buscarPorUsuario(usuarioLogado()->getId());

        if ($cliente === null) {
            $erro = "Seu cadastro de cliente não foi encontrado. Fale com a loja.";
            require __DIR__ . "/../../visao/produto/exibe.php";
            return;
        }

        $p = new Pedido();
        $p->setClienteId($cliente->getId());
        $p->setProdutoId($produto->getId());
        $p->setQuantidade($quantidade);
        $p->setValor($produto->getPreco() * $quantidade);
        $p->setOrigem("Site");
        // Sem estoque suficiente o pedido vira encomenda: espera a reposição
        $p->setEncomenda($quantidade > $produto->getEstoque() ? 1 : 0);

        $dao = new PedidoDAO();
        $dao->cadastrar($p);

        if ($p->getEncomenda() == 1) {
            $status = "Encomenda registrada! O produto será reposto e um vendedor vai entrar em contato pelo telefone " . $cliente->getTelefone() . ".";
        } else {
            $status = "Pedido enviado! Um vendedor vai entrar em contato pelo telefone " . $cliente->getTelefone() . ".";
        }
        $lista  = $dao->listarDoCliente($cliente->getId());
        require __DIR__ . "/../../visao/pedido/meus.php";
    }
}
