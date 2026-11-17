<?php
/* ============================================================
    topo.php
    Camada: VISÃO -> Layout
    Cabeçalho e menu usados por TODAS as páginas.
    Se tiver alguém logado, aparece uma barra com os links do perfil.
   ============================================================ */

// Funções de formatação usadas nas telas

// htmlspecialchars() que aceita campo vazio (null) sem dar aviso
function texto($valor)
{
    return htmlspecialchars((string) $valor);
}

function dinheiro($valor)
{
    return "R$ " . number_format($valor, 2, ",", ".");
}

function dataBr($data)
{
    return date("d/m/Y", strtotime($data));
}

// Vencimento pode ficar vazio: a conta é paga quando o produto chegar
function vencimentoBr($data)
{
    if ($data == null) {
        return "Na entrega";
    }
    return dataBr($data);
}

// Cor da etiqueta de status: verde (deu certo), vermelho (cancelado) ou amarelo (esperando)
function corStatus($status)
{
    if ($status == "Concluído" || $status == "Aprovado" || $status == "Paga") {
        return "verde";
    }
    if ($status == "Cancelado" || $status == "Rejeitado") {
        return "vermelho";
    }
    return "amarelo";
}

$logado = usuarioLogado();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ?v=... muda sempre que o arquivo é salvo, então o navegador não usa a versão velha do cache -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo filemtime(__DIR__ . "/../../assets/css/style.css"); ?>">
    <script src="assets/js/animacoes.js?v=<?php echo filemtime(__DIR__ . "/../../assets/js/animacoes.js"); ?>"></script>
    <title>AgroCoffee</title>
</head>

<body>
    <header>
        <div class="cabecalho container">
            <div class="logo">
                <a href="index.php"><img class="imgLogo" src="assets/img/logo.png" alt="Logo AgroCoffee"></a>
            </div>
            <nav class="menu">
                <a href="index.php">Home</a>
                <a href="pagina.php?fun=sobre">Sobre Nós</a>
                <a href="pagina.php?fun=servicos">Serviços</a>
                <a href="produto.php?fun=listar">Catálogo</a>
            </nav>
            <?php if ($logado === null): ?>
                <a id="botton" href="usuario.php?fun=logar">Entrar</a>
            <?php else: ?>
                <a id="botton" href="usuario.php?fun=sair">Sair</a>
            <?php endif; ?>
        </div>
    </header>


    <?php if ($logado !== null): ?>
        <section class="menuUser">
            <!-- Botão ☰: o checkbox fica escondido e o label é o que se clica.
                 Marcado = menu aberto (o CSS mostra a .barraUsuario). -->
            <input type="checkbox" id="menuUsuario" class="menuCheck">
            <label for="menuUsuario" class="botaoMenu">&#9776; Menu</label>

            <div class="barraUsuario">
                <a href="usuario.php?fun=inicio">Início</a>

                <?php if ($logado->getPerfil() == "cliente"): ?>
                    <a href="pedido.php?fun=meus">Meus pedidos</a>
                <?php endif; ?>

                <?php if (temAcesso(array("vendedor", "gerente"))): ?>
                    <a href="pedido.php?fun=listar">Pedidos</a>
                    <a href="cliente.php?fun=listar">Clientes</a>
                <?php endif; ?>

                <?php if (temAcesso(array("vendedor", "financeiro", "gerente"))): ?>
                    <a href="produto.php?fun=gerenciar">Produtos</a>
                    <a href="produto.php?fun=historico">Histórico de estoque</a>
                    <a href="orcamento.php?fun=listar">Orçamentos</a>
                    <a href="fornecedor.php?fun=listar">Fornecedores</a>
                <?php endif; ?>

                <?php if (temAcesso(array("financeiro", "gerente"))): ?>
                    <a href="conta.php?fun=listar">Contas</a>
                    <a href="conta.php?fun=relatorio">Relatório</a>
                <?php endif; ?>

                <?php if (temAcesso(array("gerente"))): ?>
                    <a href="usuario.php?fun=listar">Usuários</a>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>