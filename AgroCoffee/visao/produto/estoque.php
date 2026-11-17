<?php
/* ============================================================
    estoque.php
    Camada: VISÃO -> Produto
    Estoque de um produto: formulário de entrada/saída e histórico.
    Espera do CONTROLE: $produto (Produto) e $movimentos (array)
   ============================================================ */
?>
<main class="conteudo">
    <h2>Estoque: <?php echo texto($produto->getNome()); ?></h2>
    <p class="destaque">Quantidade atual: <?php echo $produto->getEstoque(); ?></p>
    <?php require __DIR__ . "/../layout/mensagem.php"; ?>

    <form class="formulario" method="post" action="produto.php?fun=estoque&id=<?php echo $produto->getId(); ?>">
        <h3>Entrada ou saída manual</h3>

        <label>Tipo</label>
        <select name="tipo">
            <option>Entrada</option>
            <option>Saída</option>
        </select>

        <label>Quantidade</label>
        <input type="number" name="quantidade" min="1" value="1" required>

        <label>Motivo</label>
        <input type="text" name="motivo" placeholder="Ex.: correção de inventário" required>

        <button class="botao" type="submit" name="enviar">Registrar</button>
    </form>

    <p>
        Para comprar mais unidades com um fornecedor, use o
        <a href="orcamento.php?fun=cadastrar&produto=<?php echo $produto->getId(); ?>">orçamento de reposição</a>.
    </p>

    <h3>Movimentações</h3>
    <table class="tabela">
        <tr>
            <th>Data</th>
            <th>Tipo</th>
            <th>Quantidade</th>
            <th>Saldo</th>
            <th>Motivo</th>
        </tr>
        <?php foreach ($movimentos as $m): ?>
            <tr>
                <td><?php echo date("d/m/Y H:i", strtotime($m["data"])); ?></td>
                <td><?php echo $m["tipo"]; ?></td>
                <td><?php echo $m["quantidade"]; ?></td>
                <td><?php echo $m["saldo"]; ?></td>
                <td><?php echo texto($m["motivo"]); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p><a href="produto.php?fun=gerenciar">Voltar</a></p>
</main>
