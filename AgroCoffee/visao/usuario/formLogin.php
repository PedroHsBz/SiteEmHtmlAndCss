<?php
/* ============================================================
    formLogin.php
    Camada: VISÃO -> Usuário
   ============================================================ */
?>
<main class="conteudo container">
    <form class="formulario" method="post" action="usuario.php?fun=logar">
        <h2>Entrar</h2>
        <?php require __DIR__ . "/../layout/mensagem.php"; ?>

        <label>E-mail</label>
        <input type="email" name="email" required>

        <label>Senha</label>
        <input type="password" name="senha" required>

        <button class="botao" type="submit" name="enviar">Entrar</button>
        <p>Ainda não tem conta? <a href="usuario.php?fun=cadastrar">Cadastre-se</a></p>
    </form>
</main>