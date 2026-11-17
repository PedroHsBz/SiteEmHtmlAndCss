<?php
/* ============================================================
    detalhe.php
    Camada: VISÃO -> Pedido
    Detalhe do pedido com os botões de concluir e cancelar.
    Espera do CONTROLE: $pedido (Pedido)
   ============================================================ */

// Link do WhatsApp para avisar o cliente (só números no telefone)
$telefone = "55" . preg_replace("/[^0-9]/", "", $pedido->getClienteTelefone());
$texto    = "Olá, " . $pedido->getClienteNome() . "! Seu pedido #" . $pedido->getId() . " na AgroCoffee está: " . $pedido->getStatus() . ".";
$whatsapp = "https://wa.me/" . $telefone . "?text=" . urlencode($texto);
?>
<main class="conteudo">
    <h2>Pedido #<?php echo $pedido->getId(); ?></h2>
    <?php require __DIR__ . "/../layout/mensagem.php"; ?>

    <table class="tabela">
        <tr><th>Status</th><td><span class="status <?php echo corStatus($pedido->getStatus()); ?>"><?php echo $pedido->getStatus(); ?></span></td></tr>
        <tr><th>Data</th><td><?php echo date("d/m/Y H:i", strtotime($pedido->getData())); ?></td></tr>
        <tr><th>Origem</th><td><?php echo $pedido->getOrigem(); ?></td></tr>
        <?php if ($pedido->getEncomenda() == 1): ?>
            <tr><th>Tipo</th><td><span class="status amarelo">Encomenda</span> pedido feito sem estoque</td></tr>
        <?php endif; ?>
        <tr><th>Cliente</th><td><?php echo texto($pedido->getClienteNome()); ?></td></tr>
        <tr><th>Telefone</th><td><?php echo texto($pedido->getClienteTelefone()); ?></td></tr>
        <tr><th>Produto</th><td><?php echo texto($pedido->getProdutoNome()); ?></td></tr>
        <tr><th>Quantidade</th><td><?php echo $pedido->getQuantidade(); ?></td></tr>
        <tr><th>Valor</th><td><?php echo dinheiro($pedido->getValor()); ?></td></tr>
        <?php if ($pedido->getStatus() == "Cancelado"): ?>
            <tr><th>Motivo</th><td><?php echo texto($pedido->getMotivoCancelamento()); ?></td></tr>
        <?php endif; ?>
    </table>

    <?php if ($pedido->getStatus() == "Pendente"): ?>
        <?php if ($pedido->getEncomenda() == 1): ?>
            <p class="destaque">
                Encomenda: o pedido só pode ser concluído quando o produto chegar.
                <a class="botao" href="orcamento.php?fun=cadastrar&produto=<?php echo $pedido->getProdutoId(); ?>">Registrar orçamento de reposição</a>
            </p>
        <?php endif; ?>
        <div class="acoes">
            <form class="formulario" method="post" action="pedido.php?fun=exibir&id=<?php echo $pedido->getId(); ?>">
                <h3>Concluir venda</h3>
                <p>Baixa o estoque e gera as contas a receber no financeiro (uma por parcela).</p>
                <label>Parcelas (1 = à vista)</label>
                <input type="number" name="parcelas" min="1" max="24" value="1" required>
                <label>Vencimento da 1ª parcela</label>
                <input type="date" name="vencimento" value="<?php echo date("Y-m-d"); ?>" required>
                <p>As demais parcelas vencem no mesmo dia dos meses seguintes.</p>
                <button class="botao" type="submit" name="concluir">Concluir pedido</button>
            </form>

            <form class="formulario" method="post" action="pedido.php?fun=exibir&id=<?php echo $pedido->getId(); ?>">
                <h3>Cancelar</h3>
                <label>Motivo</label>
                <input type="text" name="motivo" required>
                <button class="botao botaoPerigo" type="submit" name="cancelar">Cancelar pedido</button>
            </form>
        </div>
    <?php else: ?>
        <p><a class="botao" href="<?php echo $whatsapp; ?>" target="_blank">Avisar o cliente no WhatsApp</a></p>
    <?php endif; ?>

    <p><a href="pedido.php?fun=listar">Voltar aos pedidos</a></p>
</main>
