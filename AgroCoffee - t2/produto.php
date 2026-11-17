<?php
/* ==========================================================================
 *  produto.php
 *  ROTEADOR de Produto.
 *
 *      produto.php?fun=listar         -> ListarProduto    (catálogo do site)
 *      produto.php?fun=exibir&id=1    -> ExibirProduto    (página do produto)
 *      produto.php?fun=gerenciar      -> GerenciarProduto (painel)
 *      produto.php?fun=salvar         -> SalvarProduto    (novo / alterar)
 *      produto.php?fun=estoque&id=1   -> EstoqueProduto
 *      produto.php?fun=historico      -> HistoricoEstoque (todos os produtos)
 *      produto.php?fun=excluir&id=1   -> ExcluirProduto
 * ========================================================================== */

session_start();
require_once __DIR__ . "/controle/Acesso.php";

require_once __DIR__ . "/visao/layout/topo.php";

$fun = isset($_GET["fun"]) ? $_GET["fun"] : "listar";

switch ($fun) {
    case "exibir":
        require_once __DIR__ . "/controle/produto/ExibirProduto.php";
        new ExibirProduto();
        break;

    case "gerenciar":
        require_once __DIR__ . "/controle/produto/GerenciarProduto.php";
        new GerenciarProduto();
        break;

    case "salvar":
        require_once __DIR__ . "/controle/produto/SalvarProduto.php";
        new SalvarProduto();
        break;

    case "estoque":
        require_once __DIR__ . "/controle/produto/EstoqueProduto.php";
        new EstoqueProduto();
        break;

    case "excluir":
        require_once __DIR__ . "/controle/produto/ExcluirProduto.php";
        new ExcluirProduto();
        break;

    case "historico":
        require_once __DIR__ . "/controle/produto/HistoricoEstoque.php";
        new HistoricoEstoque();
        break;

    case "listar":
    default:
        require_once __DIR__ . "/controle/produto/ListarProduto.php";
        new ListarProduto();
        break;
}

require_once __DIR__ . "/visao/layout/base.php";
