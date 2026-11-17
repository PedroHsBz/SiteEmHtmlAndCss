<?php
/* ============================================================
    exibe.php
    Camada: VISÃO -> Cliente
    Ficha do cliente com o histórico de pedidos.
    Espera do CONTROLE: $cliente (Cliente) e $pedidos (array de Pedido)
   ============================================================ */
?>
<main class="conteudo">
    <h2><?php echo texto($cliente->getNome()); ?></h2>

    <table class="tabela">
        <tr><th>Telefone</th><td><?php echo texto($cliente->getTelefone()); ?></td></tr>
        <tr><th>Tipo</th><td>Pessoa <?php echo mb_strtolower($cliente->getTipo()); ?></td></tr>
        <tr><th>CPF / CNPJ</th><td><?php echo texto($cliente->getCpfCnpj()); ?></td></tr>
        <tr><th>E-mail</th><td><?php echo texto($cliente->getEmail()); ?></td></tr>
        <tr><th>Conta no site</th><td><?php echo $cliente->getUsuarioId() != null ? "Sim" : "Não"; ?></td></tr>
    </table>

    <a class="botao" href="cliente.php?fun=salvar&id=<?php echo $cliente->getId(); ?>">Alterar dados</a>
    <?php if (count($pedidos) == 0 && $cliente->getUsuarioId() == null): ?>
        <a class="botao botaoPerigo" href="cliente.php?fun=excluir&id=<?php echo $cliente->getId(); ?>">Excluir</a>
    <?php endif; ?>

    <h3>Pedidos</h3>
    <?php if (count($pedidos) == 0): ?>
        <p>Este cliente ainda não fez pedidos.</p>
    <?php else: ?>
        <table class="tabela">
            <tr>
                <th>Nº</th>
                <th>Data</th>
                <th>Produto</th>
                <th>Qtd.</th>
                <th>Valor</th>
                <th>Origem</th>
                <th>Status</th>
            </tr>
            <?php foreach ($pedidos as $p): ?>
                <tr>
                    <td><a href="pedido.php?fun=exibir&id=<?php echo $p->getId(); ?>">#<?php echo $p->getId(); ?></a></td>
                    <td><?php echo dataBr($p->getData()); ?></td>
                    <td><?php echo texto($p->getProdutoNome()); ?></td>
                    <td><?php echo $p->getQuantidade(); ?></td>
                    <td><?php echo dinheiro($p->getValor()); ?></td>
                    <td><?php echo $p->getOrigem(); ?></td>
                    <td><span class="status <?php echo corStatus($p->getStatus()); ?>"><?php echo $p->getStatus(); ?></span></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="cliente.php?fun=listar">Voltar aos clientes</a></p>
</main>
