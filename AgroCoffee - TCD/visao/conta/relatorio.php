<?php
/* ============================================================
    relatorio.php
    Camada: VISÃO -> Conta
    Espera do CONTROLE: $de, $ate, $lista (array de Conta) e os totais
      $recebido, $jurosRecebidos, $pago, $aReceber, $jurosAReceber,
      $aPagar, $vencidas, $saldo, $saldoPrevisto
   ============================================================ */
?>
<main class="conteudo">
    <h2>Relatório financeiro</h2>
    <p>Período: <?php echo dataBr($de); ?> a <?php echo dataBr($ate); ?> (gerado em <?php echo date("d/m/Y H:i"); ?>)</p>
    <?php require __DIR__ . "/../layout/mensagem.php"; ?>

    <form class="filtros naoImprimir" method="get" action="conta.php">
        <input type="hidden" name="fun" value="relatorio">
        De <input type="date" name="de" value="<?php echo $de; ?>">
        até <input type="date" name="ate" value="<?php echo $ate; ?>">
        <button class="botao" type="submit">Gerar</button>
        <button class="botao botaoSecundario" type="button" onclick="window.print()">Imprimir / salvar PDF</button>
        <a href="conta.php?fun=listar">Voltar às contas</a>
    </form>

    <div class="resumo">
        <div class="caixaResumo">
            <span>Recebido no período</span>
            <strong><?php echo dinheiro($recebido); ?></strong>
            <small>sendo <?php echo dinheiro($jurosRecebidos); ?> de juros</small>
        </div>
        <div class="caixaResumo">
            <span>Pago no período</span>
            <strong><?php echo dinheiro($pago); ?></strong>
        </div>
        <div class="caixaResumo">
            <span>Saldo do período</span>
            <strong><?php echo dinheiro($saldo); ?></strong>
        </div>
        <div class="caixaResumo">
            <span>A receber em aberto</span>
            <strong><?php echo dinheiro($aReceber); ?></strong>
            <small>com <?php echo dinheiro($jurosAReceber); ?> de juros até hoje</small>
        </div>
        <div class="caixaResumo">
            <span>A pagar em aberto</span>
            <strong><?php echo dinheiro($aPagar); ?></strong>
        </div>
        <div class="caixaResumo">
            <span>Saldo previsto</span>
            <strong><?php echo dinheiro($saldoPrevisto); ?></strong>
            <small><?php echo $vencidas; ?> conta(s) vencida(s)</small>
        </div>
    </div>

    <h3>Contas do período</h3>
    <?php if (count($lista) == 0): ?>
        <p>Nenhuma conta neste período.</p>
    <?php else: ?>
        <table class="tabela">
            <tr>
                <th>Tipo</th>
                <th>Descrição</th>
                <th>Vencimento</th>
                <th>Valor</th>
                <th>Juros</th>
                <th>Total</th>
                <th>Situação</th>
            </tr>
            <?php foreach ($lista as $c): ?>
                <?php $juros = $c->getStatus() == "Aberta" ? $c->calcularJuros(date("Y-m-d")) : $c->getJuros(); ?>
                <tr>
                    <td><?php echo $c->getTipo(); ?></td>
                    <td><?php echo texto($c->getDescricao()); ?></td>
                    <td><?php echo vencimentoBr($c->getVencimento()); ?></td>
                    <td><?php echo dinheiro($c->getValor()); ?></td>
                    <td><?php echo $juros > 0 ? dinheiro($juros) : "-"; ?></td>
                    <td><?php echo dinheiro($c->getValor() + $juros); ?></td>
                    <td>
                        <?php if ($c->getStatus() == "Paga"): ?>
                            <?php echo $c->getTipo() == "Pagar" ? "Paga" : "Recebida"; ?> em <?php echo dataBr($c->getPagoEm()); ?>
                        <?php elseif ($c->getVencimento() != null && $c->getVencimento() < date("Y-m-d")): ?>
                            <span class="status vermelho">Vencida</span>
                        <?php else: ?>
                            <span class="status amarelo">Aberta</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</main>
