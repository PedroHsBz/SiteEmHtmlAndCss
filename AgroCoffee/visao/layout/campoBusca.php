<?php
/* ============================================================
    campoBusca.php
    Camada: VISÃO -> Layout
    <select> "Buscar em" usado nas caixas de busca das listas.
    Espera: $camposBusca (lista do DAO -> camposBusca())
            $campo       (campo escolhido; vazio = todos)
   ============================================================ */

$campoAtual = isset($campo) ? $campo : "";
?>
<select name="campo" aria-label="Buscar em">
    <option value="">Todos os campos</option>
    <?php foreach ($camposBusca as $chave => $c): ?>
        <option value="<?php echo texto($chave); ?>" <?php if ($campoAtual == $chave) echo "selected"; ?>>
            <?php echo texto($c[0]); ?>
        </option>
    <?php endforeach; ?>
</select>
