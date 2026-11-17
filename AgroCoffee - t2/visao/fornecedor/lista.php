<?php
/* ============================================================
    lista.php
    Camada: VISÃO -> Fornecedor
    Espera do CONTROLE: $lista (array de Fornecedor) e $busca
   ============================================================ */
?>
<main class="conteudo">
    <h2>Fornecedores</h2>
    <?php require __DIR__ . "/../layout/mensagem.php"; ?>

    <a class="botao" href="fornecedor.php?fun=salvar">+ Novo fornecedor</a>

    <form class="busca" method="get" action="fornecedor.php">
        <input type="hidden" name="fun" value="listar">
        <?php $camposBusca = FornecedorDAO::camposBusca(); require __DIR__ . "/../layout/campoBusca.php"; ?>
        <input type="text" name="busca" value="<?php echo texto($busca); ?>" placeholder="Digite o que procura">
        <button class="botao" type="submit">Buscar</button>
        <?php if ($busca != ""): ?>
            <a href="fornecedor.php?fun=listar">Limpar</a>
        <?php endif; ?>
    </form>

    <table class="tabela">
        <tr>
            <th>Nome</th>
            <th>CNPJ</th>
            <th>Telefone</th>
            <th>E-mail</th>
            <th>Situação</th>
            <th></th>
        </tr>
        <?php foreach ($lista as $f): ?>
            <tr>
                <td><?php echo texto($f->getNome()); ?></td>
                <td><?php echo texto($f->getCnpj()); ?></td>
                <td><?php echo texto($f->getTelefone()); ?></td>
                <td><?php echo texto($f->getEmail()); ?></td>
                <td>
                    <?php if ($f->getAtivo() == 1): ?>
                        <span class="status verde">Ativo</span>
                    <?php else: ?>
                        <span class="status vermelho">Inativo</span>
                    <?php endif; ?>
                </td>
                <td><a href="fornecedor.php?fun=salvar&id=<?php echo $f->getId(); ?>">Alterar</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
