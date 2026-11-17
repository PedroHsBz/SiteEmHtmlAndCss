<?php
/* ============================================================
    lista.php
    Camada: VISÃO -> Conta
    Espera do CONTROLE: $lista (array de Conta), $tipo e $filtro
   ============================================================ */

// Soma do que está em aberto, para mostrar no topo
$aPagar   = 0;
$aReceber = 0;
foreach ($lista as $c) {
    if ($c->getStatus() == "Aberta" && $c->getTipo() == "Pagar") {
        $aPagar += $c->getValor();
    }
    if ($c->getStatus() == "Aberta" && $c->getTipo() == "Receber") {
        $aReceber += $c->getValor();
    }
}
?>
<main class="conteudo">
    <h2>Contas a pagar e a receber</h2>
    <?php require __DIR__ . "/../layout/mensagem.php"; ?>

    <a class="botao" href="conta.php?fun=cadastrar">+ Cadastrar conta</a>
    <a class="botao botaoSecundario" href="conta.php?fun=relatorio">Relatório</a>
    <p>Contas a receber vencidas têm juros de <?php echo Conta::JUROS_AO_MES; ?>% ao mês de atraso (cada mês começado conta inteiro).</p>

    <form class="filtros" method="get" action="conta.php">
        <input type="hidden" name="fun" value="listar">
        <?php $camposBusca = ContaDAO::camposBusca(); require __DIR__ . "/../layout/campoBusca.php"; ?>
        <input type="text" name="busca" value="<?php echo texto(isset($busca) ? $busca : ""); ?>" placeholder="Digite o que procura">
        <select name="tipo">
            <option value="">Pagar e receber</option>
            <option value="Pagar" <?php if ($tipo == "Pagar") echo "selected"; ?>>A pagar</option>
            <option value="Receber" <?php if ($tipo == "Receber") echo "selected"; ?>>A receber</option>
        </select>
        <select name="status">
            <option value="">Todas</option>
            <option value="Aberta" <?php if ($filtro == "Aberta") echo "selected"; ?>>Não pago / não recebido</option>
            <option value="Paga" <?php if ($filtro == "Paga") echo "selected"; ?>>Pago / recebido</option>
        </select>
        <button class="botao" type="submit">Filtrar</button>
    </form>

    <p class="destaque">
        Em aberto nesta lista: a pagar <?php echo dinheiro($aPagar); ?> | a receber <?php echo dinheiro($aReceber); ?>
    </p>

    <table class="tabela">
        <tr>
            <th>Tipo</th>
            <th>Descrição</th>
            <th>Parcela</th>
            <th>Valor</th>
            <th>Juros</th>
            <th>Vencimento</th>
            <th>Status</th>
            <th>Pago / recebido em</th>
            <th></th>
        </tr>
        <?php foreach ($lista as $c): ?>
            <tr>
                <td><?php echo $c->getTipo(); ?></td>
                <td><?php echo texto($c->getDescricao()); ?></td>
                <td><?php echo $c->getNumeroParcela() != null ? $c->getNumeroParcela() . "/" . $c->getTotalParcelas() : "-"; ?></td>
                <td><?php echo dinheiro($c->getValor()); ?></td>
                <td>
                    <?php
                    // Aberta: juros até hoje (vai subindo). Paga: juros que foram cobrados.
                    $juros = $c->getStatus() == "Aberta" ? $c->calcularJuros(date("Y-m-d")) : $c->getJuros();
                    if ($juros > 0) {
                        echo dinheiro($juros);
                        if ($c->getStatus() == "Aberta") {
                            echo "<br><small>" . $c->calcularMesesAtraso(date("Y-m-d")) . " mês(es) de atraso</small>";
                        }
                    } else {
                        echo "-";
                    }
                    ?>
                </td>
                <td>
                    <?php echo vencimentoBr($c->getVencimento()); ?>
                    <?php if ($c->getStatus() == "Aberta" && $c->getVencimento() != null && $c->getVencimento() < date("Y-m-d")): ?>
                        <span class="status vermelho">Vencida</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($c->getStatus() == "Aberta"): ?>
                        <span class="status amarelo">Aberta</span>
                    <?php else: ?>
                        <span class="status verde"><?php echo $c->getTipo() == "Pagar" ? "Paga" : "Recebida"; ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($c->getStatus() == "Paga"): ?>
                        <?php echo dataBr($c->getPagoEm()); ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($c->getStatus() == "Aberta"): ?>
                        <form class="formPagamento" method="post" action="conta.php?fun=pagar">
                            <input type="hidden" name="id" value="<?php echo $c->getId(); ?>">
                            <input type="date" name="pago_em" value="<?php echo date("Y-m-d"); ?>" max="<?php echo date("Y-m-d"); ?>" required>
                            <button class="botao" type="submit"><?php echo $c->getTipo() == "Pagar" ? "Pagar" : "Receber"; ?></button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
