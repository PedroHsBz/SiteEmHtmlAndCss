<?php
/* ============================================================
    lista.php
    Camada: VISÃO -> Produto
    Catálogo do site.
    Espera do CONTROLE: $lista (array de Produto)
   ============================================================ */
?>
<main class="conteudo container">
    <h2>Catálogo</h2>
    <p>Tratores e implementos prontos para a sua propriedade.</p>

    <form class="busca" method="get" action="produto.php">
        <input type="hidden" name="fun" value="listar">
        <input type="text" name="busca" value="<?php echo texto($busca); ?>" placeholder="Buscar por marca, modelo ou categoria">
        <button class="botao" type="submit">Buscar</button>
        <?php if ($busca != ""): ?>
            <a href="produto.php?fun=listar">Limpar</a>
        <?php endif; ?>
    </form>

    <?php if (count($lista) == 0): ?>
        <p>Nenhum produto encontrado.</p>
    <?php endif; ?>

    <div class="gradeProdutos container">
        <?php foreach ($lista as $p): ?>
            <article class="cardProduto">
                <img src="<?php echo texto($p->getImagem()); ?>" alt="<?php echo texto($p->getNome()); ?>">
                <div class="corpoCard">
                    <p class="categoria"><?php echo texto($p->getCategoria()); ?> - <?php echo texto($p->getCondicao()); ?></p>
                    <h3><?php echo texto($p->getNome()); ?></h3>
                    <p class="preco"><?php echo dinheiro($p->getPreco()); ?></p>
                    <?php if ($p->getEstoque() == 0): ?>
                        <p class="semEstoque">Sem estoque - sob encomenda</p>
                    <?php endif; ?>
                    <a class="botao" href="produto.php?fun=exibir&id=<?php echo $p->getId(); ?>">Ver detalhes</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</main>