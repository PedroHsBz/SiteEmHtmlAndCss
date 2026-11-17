<?php
/* ==========================================================================
 *  Acesso.php
 *  Camada: CONTROLE
 *
 *  Funções para saber quem está logado e se ele pode abrir uma tela.
 *  O usuário fica guardado na sessão com serialize() (igual à agenda).
 * ========================================================================== */

require_once __DIR__ . "/../modelo/usuario/Usuario.php";

// Devolve o Usuario logado, ou null se ninguém entrou.
function usuarioLogado() {
    if (isset($_SESSION["usuario"])) {
        return unserialize($_SESSION["usuario"]);
    }
    return null;
}

// true se o usuário logado tem um dos perfis da lista.
// Exemplo: temAcesso(array("vendedor", "gerente"))
function temAcesso($perfis) {
    $usuario = usuarioLogado();
    return $usuario !== null && in_array($usuario->getPerfil(), $perfis);
}
