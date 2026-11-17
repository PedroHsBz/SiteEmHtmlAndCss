<?php
/* ============================================================
    mensagem.php
    Camada: VISÃO -> Layout
    Mostra as mensagens que o CONTROLE manda para a tela:
      $status -> deu certo (verde)
      $erro   -> deu errado (vermelho)
   ============================================================ */

if (isset($status)) {
    echo "<p class='mensagem'>" . texto($status) . "</p>";
}
if (isset($erro)) {
    echo "<p class='erro'>" . texto($erro) . "</p>";
}
