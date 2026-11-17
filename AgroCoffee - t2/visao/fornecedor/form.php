<?php
/* ============================================================
    form.php
    Camada: VISÃO -> Fornecedor
    Espera do CONTROLE: $f (Fornecedor; vazio quando é novo)
   ============================================================ */

$novo = $f->getId() == "";
?>
<main class="conteudo">
    <form class="formulario" method="post" action="fornecedor.php?fun=salvar">
        <h2><?php echo $novo ? "Novo fornecedor" : "Alterar fornecedor"; ?></h2>
        <?php require __DIR__ . "/../layout/mensagem.php"; ?>

        <input type="hidden" name="id" value="<?php echo $f->getId(); ?>">

        <label>Nome</label>
        <input type="text" name="nome" value="<?php echo texto($f->getNome()); ?>" required>

        <label>Telefone</label>
        <input type="text" name="telefone" value="<?php echo texto($f->getTelefone()); ?>" required>

        <label>CNPJ (opcional)</label>
        <input type="text" name="cnpj" value="<?php echo texto($f->getCnpj()); ?>" placeholder="00.000.000/0000-00">

        <label>E-mail (opcional)</label>
        <input type="email" name="email" value="<?php echo texto($f->getEmail()); ?>">

        <label class="checkbox">
            <input type="checkbox" name="ativo" <?php if ($novo || $f->getAtivo() == 1) echo "checked"; ?>>
            Ativo (aparece na hora de registrar orçamento)
        </label>

        <button class="botao" type="submit" name="enviar">Salvar</button>
        <a href="fornecedor.php?fun=listar">Voltar</a>
    </form>
</main>
