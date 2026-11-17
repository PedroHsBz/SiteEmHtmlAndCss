<?php
/* ============================================================
    form.php
    Camada: VISÃO -> Usuário
    Formulário do gerente para cadastrar ou alterar usuário.
    Espera do CONTROLE: $u (Usuario; vazio quando é novo)
   ============================================================ */

$novo   = $u->getId() == "";
$perfis = array("vendedor", "financeiro", "gerente");

// Conta de cliente só nasce no cadastro do site; aqui só aparece ao alterar uma
if ($u->getPerfil() == "cliente") {
    $perfis[] = "cliente";
}
?>
<main class="conteudo">
    <form class="formulario" method="post" action="usuario.php?fun=salvar">
        <h2><?php echo $novo ? "Novo usuário" : "Alterar usuário"; ?></h2>
        <?php require __DIR__ . "/../layout/mensagem.php"; ?>

        <input type="hidden" name="id" value="<?php echo $u->getId(); ?>">

        <label>Nome</label>
        <input type="text" name="nome" value="<?php echo texto($u->getNome()); ?>" required>

        <label>E-mail</label>
        <input type="email" name="email" value="<?php echo texto($u->getEmail()); ?>" required>

        <label>Perfil</label>
        <select name="perfil">
            <?php foreach ($perfis as $perfil): ?>
                <option value="<?php echo $perfil; ?>" <?php if ($u->getPerfil() == $perfil) echo "selected"; ?>><?php echo $perfil; ?></option>
            <?php endforeach; ?>
        </select>

        <label><?php echo $novo ? "Senha (mínimo 6 caracteres)" : "Nova senha (deixe em branco para manter a atual)"; ?></label>
        <input type="password" name="senha" <?php if ($novo) echo "required"; ?>>

        <button class="botao" type="submit" name="enviar">Salvar</button>
        <a href="usuario.php?fun=listar">Voltar</a>
    </form>
</main>
