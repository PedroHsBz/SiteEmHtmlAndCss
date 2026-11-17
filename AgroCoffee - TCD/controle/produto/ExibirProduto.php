<?php
/* ==========================================================================
 *  ExibirProduto.php
 *  Camada: CONTROLE
 *
 *  Página de um produto. Se o cliente estiver logado, a visão mostra o
 *  formulário de pedido (que envia para pedido.php?fun=cadastrar).
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/produto/ProdutoDAO.php";

class ExibirProduto {

    public function __construct() {
        $dao     = new ProdutoDAO();
        $produto = isset($_GET["id"]) ? $dao->exibir($_GET["id"]) : null;

        if ($produto === null || $produto->getAtivo() == 0) {
            echo "<main class='conteudo'><p class='erro'>Produto não encontrado.</p></main>";
            return;
        }

        require __DIR__ . "/../../visao/produto/exibe.php";
    }
}
