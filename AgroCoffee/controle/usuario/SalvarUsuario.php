<?php
/* ==========================================================================
 *  SalvarUsuario.php
 *  Camada: CONTROLE
 *
 *  O gerente cadastra um usuário novo ou altera um existente.
 *    usuario.php?fun=salvar        -> novo
 *    usuario.php?fun=salvar&id=3   -> alterar o usuário 3
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/usuario/UsuarioDAO.php";

class SalvarUsuario {

    public function __construct() {

        if (!temAcesso(array("gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao = new UsuarioDAO();

        // Formulário ainda não enviado: mostra em branco ou com os dados do usuário
        if (!isset($_POST["enviar"])) {
            $u = isset($_GET["id"]) ? $dao->exibir($_GET["id"]) : new Usuario();
            require __DIR__ . "/../../visao/usuario/form.php";
            return;
        }

        $u = new Usuario();
        $u->setId($_POST["id"]);
        $u->setNome($_POST["nome"]);
        $u->setEmail($_POST["email"]);
        $u->setPerfil($_POST["perfil"]);
        $u->setSenha($_POST["senha"]);

        $novo = $_POST["id"] == "";

        // Conta de cliente nasce no cadastro do site (que também cria o Cliente).
        // Aqui o gerente cria só funcionários.
        $perfisPermitidos = array("vendedor", "financeiro", "gerente");
        if (!$novo) {
            $perfisPermitidos[] = "cliente";
        }

        if (trim($u->getNome()) == "") {
            $erro = "Informe o nome.";
        } elseif (!in_array($u->getPerfil(), $perfisPermitidos)) {
            $erro = "Perfil inválido.";
        } elseif ($dao->emailEmUso($u->getEmail(), (int) $u->getId())) {
            $erro = "Este e-mail já está cadastrado.";
        } elseif ($novo && strlen($u->getSenha()) < 6) {
            $erro = "A senha precisa ter pelo menos 6 caracteres.";
        } elseif (!$novo && $u->getSenha() != "" && strlen($u->getSenha()) < 6) {
            $erro = "A nova senha precisa ter pelo menos 6 caracteres.";
        }

        if (isset($erro)) {
            require __DIR__ . "/../../visao/usuario/form.php";
            return;
        }

        if ($novo) {
            $dao->cadastrar($u);
            $status = "Usuário " . $u->getNome() . " cadastrado.";
        } else {
            $dao->alterar($u);
            $status = "Usuário " . $u->getNome() . " alterado.";
        }

        $lista = $dao->listar();
        require __DIR__ . "/../../visao/usuario/lista.php";
    }
}
