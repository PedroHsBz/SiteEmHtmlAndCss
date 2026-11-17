<?php
/* ==========================================================================
 *  Validacao.php
 *  Camada: CONTROLE
 *
 *  Funções de validação usadas em mais de um controle.
 * ========================================================================== */

// Confere se o documento combina com o tipo de pessoa:
//   Física   -> CPF com 11 números
//   Jurídica -> CNPJ com 14 números
// Pontos, traços e barras são ignorados. Documento vazio é aceito
// (no balcão ele é opcional); quem exige preenchimento confere antes.
function documentoValido($tipo, $documento) {
    if ($documento == "") {
        return true;
    }
    $numeros = preg_replace("/[^0-9]/", "", $documento);

    if ($tipo == "Física") {
        return strlen($numeros) == 11;
    }
    if ($tipo == "Jurídica") {
        return strlen($numeros) == 14;
    }
    return false;
}

// Mensagem de erro para quando documentoValido() devolve false
function erroDocumento($tipo) {
    if ($tipo == "Jurídica") {
        return "CNPJ inválido: pessoa jurídica precisa de um CNPJ com 14 números.";
    }
    return "CPF inválido: pessoa física precisa de um CPF com 11 números.";
}
