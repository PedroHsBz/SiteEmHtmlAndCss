<?php
/* ============================================================
    formCadastro.php
    Camada: VISÃO -> Usuário
    Cadastro do cliente. Se der erro, os campos voltam preenchidos.
   ============================================================ */

function campo($nome) {
    return isset($_POST[$nome]) ? texto($_POST[$nome]) : "";
}
?>
<main class="conteudo">
    <form class="formulario" method="post" action="usuario.php?fun=cadastrar">
        <h2>Criar conta</h2>
        <?php require __DIR__ . "/../layout/mensagem.php"; ?>

        <label>Você é</label>
        <select name="tipo">
            <option value="Física">Pessoa física (CPF)</option>
            <option value="Jurídica" <?php if (campo("tipo") == "Jurídica") echo "selected"; ?>>Empresa / pessoa jurídica (CNPJ)</option>
        </select>

        <label>Nome completo ou razão social</label>
        <input type="text" name="nome" value="<?php echo campo("nome"); ?>" required>

        <label>CPF ou CNPJ</label>
        <input type="text" name="cpf_cnpj" value="<?php echo campo("cpf_cnpj"); ?>" placeholder="CPF ou CNPJ" required>

        <label>E-mail</label>
        <input type="email" name="email" value="<?php echo campo("email"); ?>" required>

        <label>Telefone</label>
        <input type="text" name="telefone" value="<?php echo campo("telefone"); ?>" placeholder="(34) 99999-0000" required>

        <label>Senha (mínimo 6 caracteres)</label>
        <input type="password" name="senha" required>

        <button class="botao" type="submit" name="enviar">Cadastrar</button>
        <p>Já tem conta? <a href="usuario.php?fun=logar">Entrar</a></p>
    </form>
</main>
