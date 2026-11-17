<?php
/* ============================================================
    detalhe.php
    Camada: VISÃO -> Orçamento
    Espera do CONTROLE: $orcamento (Orcamento) e $gerente (true/false)
   ============================================================ */
?>
<main class="conteudo">
    <h2>Orçamento #<?php echo $orcamento->getId(); ?></h2>
    <?php require __DIR__ . "/../layout/mensagem.php"; ?>

    <table class="tabela">
        <tr><th>Status</th><td><span class="status <?php echo corStatus($orcamento->getStatus()); ?>"><?php echo $orcamento->getStatus(); ?></span></td></tr>
        <tr><th>Data</th><td><?php echo dataBr($orcamento->getData()); ?></td></tr>
        <tr><th>Fornecedor</th><td><?php echo texto($orcamento->getFornecedorNome()); ?></td></tr>
        <tr><th>Produto</th><td><?php echo texto($orcamento->getProdutoNome()); ?></td></tr>
        <tr><th>Quantidade</th><td><?php echo $orcamento->getQuantidade(); ?></td></tr>
        <tr><th>Valor unitário</th><td><?php echo dinheiro($orcamento->getValorUnitario()); ?></td></tr>
        <tr><th>Total</th><td><?php echo dinheiro($orcamento->getTotal()); ?></td></tr>
        <tr><th>Vencimento</th><td><?php echo vencimentoBr($orcamento->getVencimento()); ?></td></tr>
        <?php if ($orcamento->getStatus() == "Rejeitado"): ?>
            <tr><th>Motivo</th><td><?php echo texto($orcamento->getMotivoRejeicao()); ?></td></tr>
        <?php endif; ?>
    </table>

    <?php if ($orcamento->getStatus() == "Pendente" && $gerente): ?>
        <div class="acoes">
            <form class="formulario" method="post" action="orcamento.php?fun=exibir&id=<?php echo $orcamento->getId(); ?>">
                <h3>Aprovar</h3>
                <p>Gera uma conta a pagar de <?php echo dinheiro($orcamento->getTotal()); ?> para o financeiro.</p>
                <button class="botao" type="submit" name="aprovar">Aprovar orçamento</button>
            </form>

            <form class="formulario" method="post" action="orcamento.php?fun=exibir&id=<?php echo $orcamento->getId(); ?>">
                <h3>Rejeitar</h3>
                <label>Motivo</label>
                <input type="text" name="motivo" required>
                <button class="botao botaoPerigo" type="submit" name="rejeitar">Rejeitar orçamento</button>
            </form>
        </div>
    <?php elseif ($orcamento->getStatus() == "Pendente"): ?>
        <p>Aguardando aprovação do gerente.</p>
    <?php elseif ($orcamento->getStatus() == "Aprovado"): ?>
        <p>Aprovado. Quando o financeiro pagar a conta, os produtos entram no estoque.</p>
    <?php endif; ?>

    <p><a href="orcamento.php?fun=listar">Voltar aos orçamentos</a></p>
</main>
