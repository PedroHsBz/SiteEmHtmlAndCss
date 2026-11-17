<?php
/* ==========================================================================
 *  UsuarioDAO.php
 *  Camada: MODELO -> DAO
 *
 *  Operações no banco da tabela usuario.
 *  A senha é guardada com password_hash() e conferida com password_verify().
 * ========================================================================== */

require_once __DIR__ . "/../ConnectionFactory.php";
require_once __DIR__ . "/Usuario.php";

class UsuarioDAO {

    private $con;

    public function __construct() {
        $this->con = ConnectionFactory::getConnection();
    }

    // Devolve o Usuario se e-mail e senha estiverem certos e ele estiver ativo; senão null.
    public function logar($email, $senha) {
        try {
            $stmt = $this->con->prepare("SELECT * FROM usuario WHERE email = :email AND ativo = 1");
            $stmt->bindValue(":email", $email);
            $stmt->execute();
            $linha = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$linha || !password_verify($senha, $linha["senha"])) {
                return null;
            }
            return $this->montar($linha);
        } catch (PDOException $ex) {
            echo "Erro ao realizar login: " . $ex->getMessage();
            return null;
        }
    }

    // Grava o usuário e devolve o id dele (ou 0 se der erro)
    public function cadastrar(Usuario $u) {
        try {
            $stmt = $this->con->prepare(
                "INSERT INTO usuario (nome, email, senha, perfil)
                 VALUES (:nome, :email, :senha, :perfil)"
            );
            $stmt->bindValue(":nome",   $u->getNome());
            $stmt->bindValue(":email",  $u->getEmail());
            $stmt->bindValue(":senha",  password_hash($u->getSenha(), PASSWORD_DEFAULT));
            $stmt->bindValue(":perfil", $u->getPerfil());
            $stmt->execute();
            return $this->con->lastInsertId();
        } catch (PDOException $ex) {
            echo "Erro ao cadastrar usuário: " . $ex->getMessage();
            return 0;
        }
    }

    // Altera os dados. Se a senha vier vazia, mantém a senha antiga.
    public function alterar(Usuario $u) {
        try {
            if ($u->getSenha() == "") {
                $stmt = $this->con->prepare(
                    "UPDATE usuario SET nome = :nome, email = :email, perfil = :perfil WHERE id = :id"
                );
            } else {
                $stmt = $this->con->prepare(
                    "UPDATE usuario SET nome = :nome, email = :email, perfil = :perfil, senha = :senha WHERE id = :id"
                );
                $stmt->bindValue(":senha", password_hash($u->getSenha(), PASSWORD_DEFAULT));
            }
            $stmt->bindValue(":nome",   $u->getNome());
            $stmt->bindValue(":email",  $u->getEmail());
            $stmt->bindValue(":perfil", $u->getPerfil());
            $stmt->bindValue(":id",     $u->getId());
            return $stmt->execute();
        } catch (PDOException $ex) {
            echo "Erro ao alterar usuário: " . $ex->getMessage();
            return false;
        }
    }

    // Ativa (1) ou desativa (0) o usuário
    public function alterarAtivo($id, $ativo) {
        try {
            $stmt = $this->con->prepare("UPDATE usuario SET ativo = :ativo WHERE id = :id");
            $stmt->bindValue(":ativo", $ativo);
            $stmt->bindValue(":id",    $id);
            return $stmt->execute();
        } catch (PDOException $ex) {
            echo "Erro ao alterar usuário: " . $ex->getMessage();
            return false;
        }
    }

    public function listar() {
        try {
            $stmt  = $this->con->query("SELECT * FROM usuario ORDER BY perfil, nome");
            $lista = array();
            while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $lista[] = $this->montar($linha);
            }
            return $lista;
        } catch (PDOException $ex) {
            echo "Erro ao listar usuários: " . $ex->getMessage();
            return array();
        }
    }

    public function exibir($id) {
        try {
            $stmt = $this->con->prepare("SELECT * FROM usuario WHERE id = :id");
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $linha ? $this->montar($linha) : null;
        } catch (PDOException $ex) {
            echo "Erro ao exibir usuário: " . $ex->getMessage();
            return null;
        }
    }

    // true se já existe outro usuário com esse e-mail
    public function emailEmUso($email, $idIgnorado = 0) {
        $stmt = $this->con->prepare("SELECT id FROM usuario WHERE email = :email AND id <> :id");
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":id",    $idIgnorado);
        $stmt->execute();
        return $stmt->fetch() !== false;
    }

    // Transforma uma linha do banco em objeto Usuario (sem a senha)
    private function montar($linha) {
        $u = new Usuario();
        $u->setId($linha["id"]);
        $u->setNome($linha["nome"]);
        $u->setEmail($linha["email"]);
        $u->setPerfil($linha["perfil"]);
        $u->setAtivo($linha["ativo"]);
        return $u;
    }
}
