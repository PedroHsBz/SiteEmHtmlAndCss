<?php
/* ============================================================
    lista.php
    Camada: VISÃO -> Pedido
    Todos os pedidos (vendedor e gerente).
    Espera do CONTROLE: $lista (array de Pedido) e $filtro (status escolhido)
   ============================================================ */

$opcoes = array("" => "Todos", "Pendente" => "Pendentes", "Concluído" => "Concluídos", "Cancelado" => "Cancelados");
$busca  = isset($busca) ? $busca : "";
?>
<main class="conteudo">
    <h2>Pedidos</h2>
    <?php require __DIR__ . "/../layout/mensagem.php"; ?>

    <a class="botao" href="pedido.php?fun=novo">+ Registrar pedido</a>

    <div class="filtros">
        <?php foreach ($opcoes as $valor => $nome): ?>
            <a class="<?php echo $filtro == $valor ? "filtro ativo" : "filtro"; ?>" href="pedido.php?fun=listar&status=<?php echo urlencode($valor); ?>&busca=<?php echo urlencode($busca); ?>"><?php echo $nome; ?></a>
        <?php endforeach; ?>
    </div>

    <form class="busca" method="get" action="pedido.php">
        <input type="hidden" name="fun" value="listar">
        <input type="hidden" name="status" value="<?php echo texto($filtro); ?>">
        <input type="text" name="busca" value="<?php echo texto($busca); ?>" placeholder="Buscar por nº, cliente ou produto">
        <button class="botao" type="submit">Buscar</button>
        <?php if ($busca != ""): ?>
            <a href="pedido.php?fun=listar&status=<?php echo urlencode($filtro); ?>">Limpar</a>
        <?php endif; ?>
    </form>

    <table class="tabela">
        <tr>
            <th>Nº</th>
            <th>Data</th>
            <th>Cliente</th>
            <th>Produto</th>
            <th>Qtd.</th>
            <th>Valor</th>
            <th>Origem</th>
            <th>Status</th>
        </tr>
        <?php foreach ($lista as $p): ?>
            <tr>
                <td><a href="pedido.php?fun=exibir&id=<?php echo $p->getId(); ?>">#<?php echo $p->getId(); ?></a></td>
                <td><?php echo dataBr($p->getData()); ?></td>
                <td><?php echo texto($p->getClienteNome()); ?></td>
                <td>
                    <?php echo texto($p->getProdutoNome()); ?>
                    <?php if ($p->getEncomenda() == 1): ?><span class="status amarelo">Encomenda</span><?php endif; ?>
                </td>
                <td><?php echo $p->getQuantidade(); ?></td>
                <td><?php echo dinheiro($p->getValor()); ?></td>
                <td><?php echo $p->getOrigem(); ?></td>
                <td><span class="status <?php echo corStatus($p->getStatus()); ?>"><?php echo $p->getStatus(); ?></span></td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
