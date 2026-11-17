<?php
/* ============================================================
    lista.php
    Camada: VISÃO -> Usuário
    Espera do CONTROLE: $lista (array de Usuario)
   ============================================================ */
?>
<main class="conteudo">
    <h2>Usuários</h2>
    <?php require __DIR__ . "/../layout/mensagem.php"; ?>

    <a class="botao" href="usuario.php?fun=salvar">+ Novo funcionário</a>
    <p>Contas de cliente são criadas pelo próprio cliente no site. Para cadastrar cliente do balcão, use <a href="cliente.php?fun=listar">Clientes</a>.</p>

    <table class="tabela">
        <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Perfil</th>
            <th>Situação</th>
            <th></th>
        </tr>
        <?php foreach ($lista as $u): ?>
            <tr>
                <td><?php echo texto($u->getNome()); ?></td>
                <td><?php echo texto($u->getEmail()); ?></td>
                <td><?php echo $u->getPerfil(); ?></td>
                <td>
                    <?php if ($u->getAtivo() == 1): ?>
                        <span class="status verde">Ativo</span>
                    <?php else: ?>
                        <span class="status vermelho">Desativado</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="usuario.php?fun=salvar&id=<?php echo $u->getId(); ?>">Alterar</a> |
                    <?php if ($u->getAtivo() == 1): ?>
                        <a href="usuario.php?fun=ativar&id=<?php echo $u->getId(); ?>&ativo=0">Desativar</a>
                    <?php else: ?>
                        <a href="usuario.php?fun=ativar&id=<?php echo $u->getId(); ?>&ativo=1">Ativar</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
