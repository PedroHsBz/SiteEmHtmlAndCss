<?php
/* ============================================================
    meus.php
    Camada: VISÃO -> Pedido
    Pedidos do cliente logado.
    Espera do CONTROLE: $lista (array de Pedido)
   ============================================================ */
?>
<main class="conteudo">
    <h2>Meus pedidos</h2>
    <?php require __DIR__ . "/../layout/mensagem.php"; ?>

    <?php if (count($lista) == 0): ?>
        <p>Você ainda não fez nenhum pedido. <a href="produto.php?fun=listar">Ver catálogo</a></p>
    <?php else: ?>
        <table class="tabela">
            <tr>
                <th>Nº</th>
                <th>Data</th>
                <th>Produto</th>
                <th>Qtd.</th>
                <th>Valor</th>
                <th>Status</th>
                <th></th>
            </tr>
            <?php foreach ($lista as $p): ?>
                <tr>
                    <td>#<?php echo $p->getId(); ?></td>
                    <td><?php echo dataBr($p->getData()); ?></td>
                    <td>
                        <?php echo texto($p->getProdutoNome()); ?>
                        <?php if ($p->getEncomenda() == 1): ?><span class="status amarelo">Encomenda</span><?php endif; ?>
                    </td>
                    <td><?php echo $p->getQuantidade(); ?></td>
                    <td><?php echo dinheiro($p->getValor()); ?></td>
                    <td><span class="status <?php echo corStatus($p->getStatus()); ?>"><?php echo $p->getStatus(); ?></span></td>
                    <td>
                        <?php if ($p->getStatus() == "Pendente"): ?>
                            <a href="pedido.php?fun=cancelar&id=<?php echo $p->getId(); ?>">Cancelar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</main>
