<?php
/* ============================================================
    inicio.php
    Camada: VISÃO -> Usuário
    Página inicial depois do login, com atalhos do perfil.
   ============================================================ */

$usuario = usuarioLogado();
?>
<main class="conteudo">
    <h2>Bem-vindo(a), <?php echo texto($usuario->getNome()); ?>!</h2>
    <p>O que você quer fazer hoje?</p>

    <div class="atalhos">
        <?php if ($usuario->getPerfil() == "cliente"): ?>
            <a class="atalho" href="produto.php?fun=listar">Ver catálogo e fazer pedido</a>
            <a class="atalho" href="pedido.php?fun=meus">Meus pedidos</a>
        <?php endif; ?>

        <?php if (temAcesso(array("vendedor", "gerente"))): ?>
            <a class="atalho" href="pedido.php?fun=listar&status=Pendente">Pedidos pendentes</a>
            <a class="atalho" href="pedido.php?fun=novo">Registrar pedido (WhatsApp / balcão)</a>
            <a class="atalho" href="orcamento.php?fun=cadastrar">Registrar orçamento de reposição</a>
            <a class="atalho" href="cliente.php?fun=listar">Clientes</a>
        <?php endif; ?>

        <?php if (temAcesso(array("vendedor", "financeiro", "gerente"))): ?>
            <a class="atalho" href="produto.php?fun=gerenciar">Produtos e estoque</a>
            <a class="atalho" href="produto.php?fun=historico">Histórico de estoque</a>
            <a class="atalho" href="orcamento.php?fun=listar">Orçamentos</a>
            <a class="atalho" href="fornecedor.php?fun=listar">Fornecedores</a>
        <?php endif; ?>

        <?php if (temAcesso(array("financeiro", "gerente"))): ?>
            <a class="atalho" href="conta.php?fun=listar&status=Aberta">Contas em aberto</a>
            <a class="atalho" href="conta.php?fun=cadastrar">Cadastrar conta</a>
            <a class="atalho" href="conta.php?fun=relatorio">Relatório financeiro</a>
        <?php endif; ?>

        <?php if (temAcesso(array("gerente"))): ?>
            <a class="atalho" href="usuario.php?fun=listar">Usuários</a>
        <?php endif; ?>
    </div>
</main>
