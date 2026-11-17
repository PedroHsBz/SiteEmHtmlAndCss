<?php
/* ============================================================
    confirmaCancelamento.php
    Camada: VISÃO -> Pedido
    Pergunta ao cliente se ele quer mesmo cancelar.
    Espera do CONTROLE: $pedido (Pedido)
   ============================================================ */
?>
<main class="conteudo">
    <h2>Cancelar pedido #<?php echo $pedido->getId(); ?></h2>
    <p>
        Tem certeza que deseja cancelar o pedido de
        <strong><?php echo $pedido->getQuantidade(); ?>x <?php echo texto($pedido->getProdutoNome()); ?></strong>
        (<?php echo dinheiro($pedido->getValor()); ?>)?
    </p>
    <a class="botao botaoPerigo" href="pedido.php?fun=cancelar&id=<?php echo $pedido->getId(); ?>&conf=sim">Sim, cancelar</a>
    <a class="botao botaoSecundario" href="pedido.php?fun=meus">Não, voltar</a>
</main>
