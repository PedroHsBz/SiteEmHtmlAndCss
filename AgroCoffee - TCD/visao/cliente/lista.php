<?php
/* ============================================================
    lista.php
    Camada: VISÃO -> Cliente
    Espera do CONTROLE: $lista (array de Cliente) e $busca
   ============================================================ */
?>
<main class="conteudo">
    <h2>Clientes</h2>
    <?php require __DIR__ . "/../layout/mensagem.php"; ?>

    <a class="botao" href="cliente.php?fun=salvar">+ Novo cliente</a>

    <form class="busca" method="get" action="cliente.php">
        <input type="hidden" name="fun" value="listar">
        <input type="text" name="busca" value="<?php echo texto($busca); ?>" placeholder="Buscar por nome, CPF/CNPJ ou telefone">
        <button class="botao" type="submit">Buscar</button>
        <?php if ($busca != ""): ?>
            <a href="cliente.php?fun=listar">Limpar</a>
        <?php endif; ?>
    </form>

    <table class="tabela">
        <tr>
            <th>Nome</th>
            <th>Tipo</th>
            <th>CPF / CNPJ</th>
            <th>Telefone</th>
            <th>E-mail</th>
            <th>Conta no site</th>
            <th></th>
        </tr>
        <?php foreach ($lista as $c): ?>
            <tr>
                <td><a href="cliente.php?fun=exibir&id=<?php echo $c->getId(); ?>"><?php echo texto($c->getNome()); ?></a></td>
                <td><?php echo $c->getTipo(); ?></td>
                <td><?php echo texto($c->getCpfCnpj()); ?></td>
                <td><?php echo texto($c->getTelefone()); ?></td>
                <td><?php echo texto($c->getEmail()); ?></td>
                <td><?php echo $c->getUsuarioId() != null ? "Sim" : "Não"; ?></td>
                <td>
                    <a href="cliente.php?fun=salvar&id=<?php echo $c->getId(); ?>">Alterar</a> |
                    <a href="cliente.php?fun=excluir&id=<?php echo $c->getId(); ?>">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
