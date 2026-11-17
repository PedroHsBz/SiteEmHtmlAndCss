<?php
/* ============================================================
    lista.php
    Camada: VISÃO -> Orçamento
    Espera do CONTROLE: $lista (array de Orcamento) e $busca (opcional)
   ============================================================ */

$busca = isset($busca) ? $busca : "";
?>
<main class="conteudo">
    <h2>Orçamentos de reposição</h2>
    <?php require __DIR__ . "/../layout/mensagem.php"; ?>

    <?php if (temAcesso(array("vendedor", "gerente"))): ?>
        <a class="botao" href="orcamento.php?fun=cadastrar">+ Registrar orçamento</a>
    <?php endif; ?>

    <form class="busca" method="get" action="orcamento.php">
        <input type="hidden" name="fun" value="listar">
        <?php $camposBusca = OrcamentoDAO::camposBusca(); require __DIR__ . "/../layout/campoBusca.php"; ?>
        <input type="text" name="busca" value="<?php echo texto($busca); ?>" placeholder="Digite o que procura">
        <button class="botao" type="submit">Buscar</button>
        <?php if ($busca != ""): ?>
            <a href="orcamento.php?fun=listar">Limpar</a>
        <?php endif; ?>
    </form>

    <table class="tabela">
        <tr>
            <th>Nº</th>
            <th>Data</th>
            <th>Fornecedor</th>
            <th>Produto</th>
            <th>Qtd.</th>
            <th>Total</th>
            <th>Status</th>
        </tr>
        <?php foreach ($lista as $o): ?>
            <tr>
                <td><a href="orcamento.php?fun=exibir&id=<?php echo $o->getId(); ?>">#<?php echo $o->getId(); ?></a></td>
                <td><?php echo dataBr($o->getData()); ?></td>
                <td><?php echo texto($o->getFornecedorNome()); ?></td>
                <td><?php echo texto($o->getProdutoNome()); ?></td>
                <td><?php echo $o->getQuantidade(); ?></td>
                <td><?php echo dinheiro($o->getTotal()); ?></td>
                <td><span class="status <?php echo corStatus($o->getStatus()); ?>"><?php echo $o->getStatus(); ?></span></td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
