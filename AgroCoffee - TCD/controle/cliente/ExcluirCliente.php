<?php
/* ==========================================================================
 *  ExcluirCliente.php
 *  Camada: CONTROLE
 *
 *  Exclusão em 2 passos, igual à agenda:
 *    1. cliente.php?fun=excluir&id=3           -> pede confirmação
 *    2. cliente.php?fun=excluir&id=3&conf=sim  -> exclui e volta para a lista
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/cliente/ClienteDAO.php";

class ExcluirCliente {

    public function __construct() {

        if (!temAcesso(array("vendedor", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao     = new ClienteDAO();
        $cliente = isset($_GET["id"]) ? $dao->exibir($_GET["id"]) : null;
        $busca   = "";

        if ($cliente === null) {
            $erro  = "Cliente não encontrado.";
            $lista = $dao->listar();
            require __DIR__ . "/../../visao/cliente/lista.php";
            return;
        }

        // Passo 2 — confirmado: exclui e mostra a lista
        if (isset($_GET["conf"]) && $_GET["conf"] == "sim") {
            $erro = $dao->excluir($cliente);
            if ($erro === null) {
                $status = "Cliente " . $cliente->getNome() . " excluído.";
            }
            $lista = $dao->listar();
            require __DIR__ . "/../../visao/cliente/lista.php";
            return;
        }

        // Passo 1 — pede confirmação
        require __DIR__ . "/../../visao/cliente/confirmaExclusao.php";
    }
}
