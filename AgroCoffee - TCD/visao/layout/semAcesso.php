<?php
/* ============================================================
    semAcesso.php
    Camada: VISÃO -> Layout
    Mostrada quando o usuário não entrou ou o perfil não pode abrir a tela.
   ============================================================ */
?>
<main class="conteudo">
    <h2>Acesso negado</h2>
    <?php if (usuarioLogado() === null): ?>
        <p class="erro">Você precisa entrar no sistema para abrir esta página.</p>
        <a class="botao" href="usuario.php?fun=logar">Entrar</a>
    <?php else: ?>
        <p class="erro">O seu perfil não tem permissão para abrir esta página.</p>
        <a class="botao" href="usuario.php?fun=inicio">Voltar ao início</a>
    <?php endif; ?>
</main>
