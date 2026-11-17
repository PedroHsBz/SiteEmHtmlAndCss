<?php
/* ============================================================
    form.php
    Camada: VISÃO -> Cliente
    Espera do CONTROLE: $c (Cliente; vazio quando é novo)
   ============================================================ */

$novo = $c->getId() == "";
?>
<main class="conteudo">
    <form class="formulario" method="post" action="cliente.php?fun=salvar">
        <h2><?php echo $novo ? "Novo cliente" : "Alterar cliente"; ?></h2>
        <?php require __DIR__ . "/../layout/mensagem.php"; ?>

        <input type="hidden" name="id" value="<?php echo $c->getId(); ?>">

        <label>Tipo de pessoa</label>
        <select name="tipo">
            <option value="Física">Pessoa física (CPF)</option>
            <option value="Jurídica" <?php if ($c->getTipo() == "Jurídica") echo "selected"; ?>>Pessoa jurídica (CNPJ)</option>
        </select>

        <label>Nome ou razão social</label>
        <input type="text" name="nome" value="<?php echo texto($c->getNome()); ?>" required>

        <label>Telefone</label>
        <input type="text" name="telefone" value="<?php echo texto($c->getTelefone()); ?>" placeholder="(34) 99999-0000" required>

        <label>CPF ou CNPJ (opcional)</label>
        <input type="text" name="cpf_cnpj" value="<?php echo texto($c->getCpfCnpj()); ?>" placeholder="CPF ou CNPJ">

        <label>E-mail (opcional)</label>
        <input type="email" name="email" value="<?php echo texto($c->getEmail()); ?>">

        <p>Se este cliente criar conta no site com o mesmo CPF/CNPJ, a conta é ligada a este cadastro.</p>

        <button class="botao" type="submit" name="enviar">Salvar</button>
        <a href="cliente.php?fun=listar">Voltar</a>
    </form>
</main>
