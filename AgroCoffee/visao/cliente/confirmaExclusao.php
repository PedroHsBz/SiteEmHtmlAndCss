<?php
/* ============================================================
    confirmaExclusao.php
    Camada: VISÃO -> Cliente
    Espera do CONTROLE: $cliente (Cliente)
   ============================================================ */
?>
<main class="conteudo">
    <h2>Excluir cliente</h2>
    <p>Tem certeza que deseja excluir <strong><?php echo texto($cliente->getNome()); ?></strong>?</p>
    <a class="botao botaoPerigo" href="cliente.php?fun=excluir&id=<?php echo $cliente->getId(); ?>&conf=sim">Sim, excluir</a>
    <a class="botao botaoSecundario" href="cliente.php?fun=listar">Não, voltar</a>
</main>
