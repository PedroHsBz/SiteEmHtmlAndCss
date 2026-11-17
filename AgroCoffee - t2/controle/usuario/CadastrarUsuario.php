<?php
/* ==========================================================================
 *  CadastrarUsuario.php
 *  Camada: CONTROLE
 *
 *  Cadastro feito pelo próprio CLIENTE no site. Grava duas coisas:
 *    - o Usuario (e-mail e senha, para o login)
 *    - o Cliente (nome, CPF/CNPJ, telefone, para os pedidos)
 *  Se o vendedor já tinha cadastrado esse CPF/CNPJ no balcão, a conta nova é
 *  ligada ao cliente que já existe (e ele passa a ver os pedidos do balcão).
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/usuario/UsuarioDAO.php";
require_once __DIR__ . "/../../modelo/cliente/ClienteDAO.php";
require_once __DIR__ . "/../Validacao.php";

class CadastrarUsuario {

    public function __construct() {

        if (!isset($_POST["enviar"])) {
            require __DIR__ . "/../../visao/usuario/formCadastro.php";
            return;
        }

        $usuarioDAO = new UsuarioDAO();
        $clienteDAO = new ClienteDAO();

        $cpfCnpj   = trim($_POST["cpf_cnpj"]);
        $tipo      = $_POST["tipo"] == "Jurídica" ? "Jurídica" : "Física";
        $existente = $clienteDAO->buscarPorCpfCnpj($cpfCnpj);   // cadastrado antes no balcão?

        if (trim($_POST["nome"]) == "" || $cpfCnpj == "" || trim($_POST["telefone"]) == "") {
            $erro = "Preencha todos os campos.";
        } elseif (!documentoValido($tipo, $cpfCnpj)) {
            $erro = erroDocumento($tipo);
        } elseif ($usuarioDAO->emailEmUso($_POST["email"])) {
            $erro = "Este e-mail já está cadastrado.";
        } elseif ($existente !== null && $existente->getUsuarioId() != null) {
            $erro = "Este CPF/CNPJ já tem uma conta. Use a opção Entrar.";
        } elseif (strlen($_POST["senha"]) < 6) {
            $erro = "A senha precisa ter pelo menos 6 caracteres.";
        }

        if (isset($erro)) {
            require __DIR__ . "/../../visao/usuario/formCadastro.php";
            return;
        }

        /* ----------- 1. Usuário (login) ----------- */
        $u = new Usuario();
        $u->setNome($_POST["nome"]);
        $u->setEmail($_POST["email"]);
        $u->setSenha($_POST["senha"]);
        $u->setPerfil("cliente");
        $usuarioId = $usuarioDAO->cadastrar($u);

        /* ----------- 2. Cliente (compras) ----------- */
        if ($existente !== null) {
            $clienteDAO->vincularUsuario($existente->getId(), $usuarioId);
        } else {
            $c = new Cliente();
            $c->setNome($_POST["nome"]);
            $c->setTipo($tipo);
            $c->setCpfCnpj($cpfCnpj);
            $c->setTelefone($_POST["telefone"]);
            $c->setEmail($_POST["email"]);
            $c->setUsuarioId($usuarioId);
            $clienteDAO->cadastrar($c);
        }

        $status = "Cadastro feito com sucesso! Agora é só entrar.";
        require __DIR__ . "/../../visao/usuario/formLogin.php";
    }
}
