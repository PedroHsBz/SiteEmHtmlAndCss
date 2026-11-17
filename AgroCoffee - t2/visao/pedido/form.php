<?php
/* ============================================================
    form.php
    Camada: VISÃO -> Pedido
    O vendedor registra um pedido de WhatsApp ou balcão.
    Espera do CONTROLE: $produtos (array de Produto) e $clientes (array de Cliente)
   ============================================================ */

function campo($nome) {
    return isset($_POST[$nome]) ? texto($_POST[$nome]) : "";
}
?>
<main class="conteudo">
    <form class="formulario" method="post" action="pedido.php?fun=novo">
        <h2>Registrar pedido</h2>
        <?php require __DIR__ . "/../layout/mensagem.php"; ?>

        <label>Origem</label>
        <select name="origem">
            <option>WhatsApp</option>
            <option <?php if (campo("origem") == "Balcão") echo "selected"; ?>>Balcão</option>
        </select>

        <h3>Cliente</h3>
        <label>Cliente já cadastrado</label>
        <select name="cliente_id">
            <option value="">-- cliente novo (preencha abaixo) --</option>
            <?php foreach ($clientes as $c): ?>
                <option value="<?php echo $c->getId(); ?>" <?php if (campo("cliente_id") == $c->getId()) echo "selected"; ?>>
                    <?php echo texto($c->getNome()); ?> - <?php echo texto($c->getTelefone()); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div class="caixaNovo">
            <p>Não achou na lista? Cadastre agora:</p>
            <label>Tipo de pessoa</label>
            <select name="novo_tipo">
                <option value="Física">Pessoa física (CPF)</option>
                <option value="Jurídica" <?php if (campo("novo_tipo") == "Jurídica") echo "selected"; ?>>Pessoa jurídica (CNPJ)</option>
            </select>

            <label>Nome ou razão social</label>
            <input type="text" name="novo_nome" value="<?php echo campo("novo_nome"); ?>">

            <label>Telefone</label>
            <input type="text" name="novo_telefone" value="<?php echo campo("novo_telefone"); ?>" placeholder="(34) 99999-0000">

            <label>CPF ou CNPJ (opcional)</label>
            <input type="text" name="novo_cpf_cnpj" value="<?php echo campo("novo_cpf_cnpj"); ?>" placeholder="CPF ou CNPJ">
        </div>

        <h3>Produto</h3>
        <label>Produto</label>
        <select name="produto_id" required>
            <option value="">Escolha...</option>
            <?php foreach ($produtos as $p): ?>
                <option value="<?php echo $p->getId(); ?>" <?php if (campo("produto_id") == $p->getId()) echo "selected"; ?>>
                    <?php echo texto($p->getNome()); ?> - <?php echo dinheiro($p->getPreco()); ?> (estoque: <?php echo $p->getEstoque(); ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label>Quantidade</label>
        <input type="number" name="quantidade" min="1" value="<?php echo campo("quantidade") == "" ? 1 : campo("quantidade"); ?>" required>

        <button class="botao" type="submit" name="enviar">Registrar</button>
        <a href="pedido.php?fun=listar">Voltar</a>
    </form>
</main>
