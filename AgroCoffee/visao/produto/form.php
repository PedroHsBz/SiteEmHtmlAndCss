<?php
/* ============================================================
    form.php
    Camada: VISÃO -> Produto
    Cadastrar ou alterar produto.
    Espera do CONTROLE: $p (Produto; vazio quando é novo)
   ============================================================ */

$novo       = $p->getId() == "";
$categorias = array("Tratores", "Implementos");
$condicoes  = array("Novo", "Seminovo");
?>
<main class="conteudo">
    <form class="formulario" method="post" action="produto.php?fun=salvar" enctype="multipart/form-data">
        <h2><?php echo $novo ? "Novo produto" : "Alterar produto"; ?></h2>
        <?php require __DIR__ . "/../layout/mensagem.php"; ?>

        <input type="hidden" name="id" value="<?php echo $p->getId(); ?>">
        <input type="hidden" name="imagemAtual" value="<?php echo texto($p->getImagem()); ?>">

        <label>Categoria</label>
        <select name="categoria">
            <?php foreach ($categorias as $categoria): ?>
                <option <?php if ($p->getCategoria() == $categoria) echo "selected"; ?>><?php echo $categoria; ?></option>
            <?php endforeach; ?>
        </select>

        <label>Marca</label>
        <input type="text" name="marca" value="<?php echo texto($p->getMarca()); ?>" required>

        <label>Modelo</label>
        <input type="text" name="modelo" value="<?php echo texto($p->getModelo()); ?>" required>

        <label>Condição</label>
        <select name="condicao">
            <?php foreach ($condicoes as $condicao): ?>
                <option <?php if ($p->getCondicao() == $condicao) echo "selected"; ?>><?php echo $condicao; ?></option>
            <?php endforeach; ?>
        </select>

        <label>Ano</label>
        <input type="number" name="ano" value="<?php echo $novo ? date("Y") : $p->getAno(); ?>" required>

        <label>Horas de uso</label>
        <input type="number" name="horas" min="0" value="<?php echo $novo ? 0 : $p->getHoras(); ?>" required>

        <label>Preço (R$)</label>
        <input type="number" name="preco" step="0.01" min="0" value="<?php echo $p->getPreco(); ?>" required>

        <label>Descrição</label>
        <textarea name="descricao" rows="4"><?php echo texto($p->getDescricao()); ?></textarea>

        <label>Foto <?php if (!$novo) echo "(envie só se quiser trocar)"; ?></label>
        <?php if (!$novo): ?>
            <img class="fotoAtual" src="<?php echo texto($p->getImagem()); ?>" alt="Foto atual">
        <?php endif; ?>
        <input type="file" name="imagem" accept="image/*">

        <label class="checkbox">
            <input type="checkbox" name="ativo" <?php if ($novo || $p->getAtivo() == 1) echo "checked"; ?>>
            Mostrar no site
        </label>

        <button class="botao" type="submit" name="enviar">Salvar</button>
        <a href="produto.php?fun=gerenciar">Voltar</a>
    </form>
</main>
