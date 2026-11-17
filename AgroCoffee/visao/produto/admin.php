<?php
/* ============================================================
    admin.php
    Camada: VISÃO -> Produto
    Lista de produtos do painel (inclui os ocultos).
    Espera do CONTROLE: $lista (array de Produto) e $busca (opcional)
   ============================================================ */

$busca = isset($busca) ? $busca : "";
?>
<main class="conteudo">
    <h2>Produtos e estoque</h2>
    <?php require __DIR__ . "/../layout/mensagem.php"; ?>

    <a class="botao" href="produto.php?fun=salvar">+ Novo produto</a>
    <a class="botao botaoSecundario" href="produto.php?fun=historico">Histórico de estoque</a>

    <form class="busca" method="get" action="produto.php">
        <input type="hidden" name="fun" value="gerenciar">
        <?php $camposBusca = ProdutoDAO::camposBusca(); require __DIR__ . "/../layout/campoBusca.php"; ?>
        <input type="text" name="busca" value="<?php echo texto($busca); ?>" placeholder="Digite o que procura">
        <button class="botao" type="submit">Buscar</button>
        <?php if ($busca != ""): ?>
            <a href="produto.php?fun=gerenciar">Limpar</a>
        <?php endif; ?>
    </form>

    <table class="tabela">
        <tr>
            <th>Produto</th>
            <th>Categoria</th>
            <th>Preço</th>
            <th>Estoque</th>
            <th>No site</th>
            <th></th>
        </tr>
        <?php foreach ($lista as $p): ?>
            <tr>
                <td><?php echo texto($p->getNome()); ?></td>
                <td><?php echo texto($p->getCategoria()); ?></td>
                <td><?php echo dinheiro($p->getPreco()); ?></td>
                <td>
                    <?php if ($p->getEstoque() == 0): ?>
                        <span class="status vermelho">0</span>
                    <?php else: ?>
                        <?php echo $p->getEstoque(); ?>
                    <?php endif; ?>
                </td>
                <td><?php echo $p->getAtivo() == 1 ? "Visível" : "Oculto"; ?></td>
                <td>
                    <a href="produto.php?fun=salvar&id=<?php echo $p->getId(); ?>">Alterar</a> |
                    <a href="produto.php?fun=estoque&id=<?php echo $p->getId(); ?>">Estoque</a> |
                    <a href="produto.php?fun=excluir&id=<?php echo $p->getId(); ?>">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
