<?php
/* ============================================================
    form.php
    Camada: VISÃO -> Orçamento
    Espera do CONTROLE: $produtos (array de Produto) e $fornecedores (array de Fornecedor)
   ============================================================ */

function campo($nome) {
    return isset($_POST[$nome]) ? texto($_POST[$nome]) : "";
}

// Produto já escolhido: vem do POST (erro no envio) ou do link da tela de estoque
$produtoEscolhido = isset($_POST["produto_id"]) ? $_POST["produto_id"] : (isset($_GET["produto"]) ? $_GET["produto"] : "");
?>
<main class="conteudo">
    <form class="formulario" method="post" action="orcamento.php?fun=cadastrar">
        <h2>Registrar orçamento de reposição</h2>
        <?php require __DIR__ . "/../layout/mensagem.php"; ?>

        <label>Fornecedor</label>
        <select name="fornecedor_id" required>
            <option value="">Escolha...</option>
            <?php foreach ($fornecedores as $f): ?>
                <option value="<?php echo $f->getId(); ?>" <?php if (campo("fornecedor_id") == $f->getId()) echo "selected"; ?>>
                    <?php echo texto($f->getNome()); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p>Não está na lista? <a href="fornecedor.php?fun=salvar">Cadastre o fornecedor</a> e volte aqui.</p>

        <label>Produto</label>
        <select name="produto_id" required>
            <option value="">Escolha...</option>
            <?php foreach ($produtos as $p): ?>
                <option value="<?php echo $p->getId(); ?>" <?php if ($produtoEscolhido == $p->getId()) echo "selected"; ?>>
                    <?php echo texto($p->getNome()); ?> (estoque: <?php echo $p->getEstoque(); ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label>Quantidade</label>
        <input type="number" name="quantidade" min="1" value="<?php echo campo("quantidade") == "" ? 1 : campo("quantidade"); ?>" required>

        <label>Valor unitário (R$)</label>
        <input type="number" name="valor_unitario" step="0.01" min="0" value="<?php echo campo("valor_unitario"); ?>" required>

        <label>Vencimento do pagamento (deixe vazio se o fornecedor recebe na entrega)</label>
        <input type="date" name="vencimento" value="<?php echo campo("vencimento"); ?>">

        <button class="botao" type="submit" name="enviar">Registrar</button>
        <a href="orcamento.php?fun=listar">Voltar</a>
    </form>
</main>
