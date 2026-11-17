<?php
/* ============================================================
    confirmaExclusao.php
    Camada: VISÃO -> Produto
    Espera do CONTROLE: $produto (Produto)
   ============================================================ */
?>
<main class="conteudo">
    <h2>Excluir produto</h2>
    <p>Tem certeza que deseja excluir <strong><?php echo texto($produto->getNome()); ?></strong>?</p>
    <p>O histórico de estoque deste produto também será apagado.</p>
    <a class="botao botaoPerigo" href="produto.php?fun=excluir&id=<?php echo $produto->getId(); ?>&conf=sim">Sim, excluir</a>
    <a class="botao botaoSecundario" href="produto.php?fun=gerenciar">Não, voltar</a>
</main>
