<?php
/* ============================================================
    form.php
    Camada: VISÃO -> Conta
    Cadastro de conta avulsa.
   ============================================================ */

function campo($nome) {
    return isset($_POST[$nome]) ? texto($_POST[$nome]) : "";
}
?>
<main class="conteudo">
    <form class="formulario" method="post" action="conta.php?fun=cadastrar">
        <h2>Cadastrar conta</h2>
        <?php require __DIR__ . "/../layout/mensagem.php"; ?>

        <label>Tipo</label>
        <select name="tipo">
            <option value="Pagar">A pagar</option>
            <option value="Receber" <?php if (campo("tipo") == "Receber") echo "selected"; ?>>A receber</option>
        </select>

        <label>Descrição</label>
        <input type="text" name="descricao" value="<?php echo campo("descricao"); ?>" placeholder="Ex.: Aluguel do galpão" required>

        <label>Valor (R$)</label>
        <input type="number" name="valor" step="0.01" min="0" value="<?php echo campo("valor"); ?>" required>

        <label>Vencimento (deixe vazio se for pagar na entrega)</label>
        <input type="date" name="vencimento" value="<?php echo campo("vencimento"); ?>">

        <button class="botao" type="submit" name="enviar">Cadastrar</button>
        <a href="conta.php?fun=listar">Voltar</a>
    </form>
</main>
