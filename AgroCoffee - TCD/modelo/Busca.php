<?php
/* ==========================================================================
 *  Busca.php
 *  Camada: MODELO
 *
 *  Monta o pedaço do WHERE das buscas das listas.
 *
 *  Cada DAO tem uma função camposBusca() que devolve os campos que podem
 *  ser escolhidos na tela, no formato:
 *      "chave" => array("Nome na tela", "expressão SQL")
 *
 *  $campo vem da tela (<select name="campo">):
 *    - se for uma chave da lista  -> procura só naquele campo
 *    - se for vazio ou inválido   -> procura em todos os campos juntos
 *  Como só usamos expressões que estão na lista, o usuário não consegue
 *  colocar SQL próprio pelo <select>.
 * ========================================================================== */

function sqlBusca($campos, $campo) {
    if (isset($campos[$campo])) {
        return $campos[$campo][1] . " LIKE :busca";
    }

    // "Todos os campos": CONCAT_WS junta tudo com espaço e ignora os vazios (NULL)
    $expressoes = array();
    foreach ($campos as $c) {
        $expressoes[] = $c[1];
    }
    return "CONCAT_WS(' ', " . implode(", ", $expressoes) . ") LIKE :busca";
}
