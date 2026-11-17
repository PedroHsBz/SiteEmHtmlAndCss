<?php
/* ============================================================
    historico.php
    Camada: VISÃO -> Produto
    Histórico de movimentações de estoque de todos os produtos.
    Espera do CONTROLE: $movimentos (array)
   ============================================================ */
?>
<main class="conteudo">
    <h2>Histórico de estoque</h2>
    <p>Todas as entradas e saídas: vendas, reposições e ajustes manuais.</p>

    <?php if (count($movimentos) == 0): ?>
        <p>Nenhuma movimentação registrada ainda.</p>
    <?php else: ?>
        <table class="tabela">
            <tr>
                <th>Data</th>
                <th>Produto</th>
                <th>Tipo</th>
                <th>Quantidade</th>
                <th>Saldo</th>
                <th>Motivo</th>
            </tr>
            <?php foreach ($movimentos as $m): ?>
                <tr>
                    <td><?php echo date("d/m/Y H:i", strtotime($m["data"])); ?></td>
                    <td><a href="produto.php?fun=estoque&id=<?php echo $m["produto_id"]; ?>"><?php echo texto($m["produto_nome"]); ?></a></td>
                    <td>
                        <?php if ($m["tipo"] == "Entrada"): ?>
                            <span class="status verde">Entrada</span>
                        <?php else: ?>
                            <span class="status vermelho">Saída</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $m["quantidade"]; ?></td>
                    <td><?php echo $m["saldo"]; ?></td>
                    <td><?php echo texto($m["motivo"]); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="produto.php?fun=gerenciar">Voltar aos produtos</a></p>
</main>
