<?php
/* ============================================================
    exibe.php
    Camada: VISÃO -> Produto
    Página de um produto, com o formulário de pedido para o cliente.
    Espera do CONTROLE: $produto (Produto)
   ============================================================ */
?>
<main class="conteudo">
    <div class="detalheProduto">
        <img src="<?php echo texto($produto->getImagem()); ?>" alt="<?php echo texto($produto->getNome()); ?>">

        <div>
            <p class="categoria"><?php echo texto($produto->getCategoria()); ?></p>
            <h2><?php echo texto($produto->getNome()); ?></h2>
            <p class="preco"><?php echo dinheiro($produto->getPreco()); ?></p>
            <p><?php echo texto($produto->getDescricao()); ?></p>

            <table class="tabela">
                <tr><th>Condição</th><td><?php echo texto($produto->getCondicao()); ?></td></tr>
                <tr><th>Ano</th><td><?php echo $produto->getAno(); ?></td></tr>
                <tr><th>Horas de uso</th><td><?php echo $produto->getHoras(); ?> h</td></tr>
                <tr><th>Em estoque</th><td><?php echo $produto->getEstoque(); ?></td></tr>
            </table>

            <?php require __DIR__ . "/../layout/mensagem.php"; ?>

            <?php if (temAcesso(array("cliente"))): ?>
                <form class="formPedido" method="post" action="pedido.php?fun=cadastrar">
                    <input type="hidden" name="produto_id" value="<?php echo $produto->getId(); ?>">
                    <label>Quantidade</label>
                    <input type="number" name="quantidade" value="1" min="1" required>
                    <?php if ($produto->getEstoque() == 0): ?>
                        <p class="semEstoque">Sem estoque no momento. Você pode encomendar: a loja repõe o produto e o vendedor entra em contato.</p>
                        <button class="botao" type="submit">Encomendar</button>
                    <?php else: ?>
                        <p>Acima de <?php echo $produto->getEstoque(); ?> unidade(s), o pedido vira encomenda.</p>
                        <button class="botao" type="submit">Fazer pedido</button>
                    <?php endif; ?>
                </form>
            <?php elseif (usuarioLogado() === null): ?>
                <p><a class="botao" href="usuario.php?fun=logar">Entre para fazer o pedido</a></p>
            <?php endif; ?>

            <p><a href="produto.php?fun=listar">Voltar ao catálogo</a></p>
        </div>
    </div>
</main>
