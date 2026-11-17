<?php
/* ==========================================================================
 *  ClienteDAO.php
 *  Camada: MODELO -> DAO
 *
 *  Operações no banco da tabela cliente.
 *  CPF/CNPJ e e-mail são opcionais: campo vazio é gravado como NULL.
 * ========================================================================== */

require_once __DIR__ . "/../ConnectionFactory.php";
require_once __DIR__ . "/Cliente.php";

class ClienteDAO {

    private $con;

    public function __construct() {
        $this->con = ConnectionFactory::getConnection();
    }

    // Grava o cliente e devolve o id dele (ou 0 se der erro)
    public function cadastrar(Cliente $c) {
        try {
            $stmt = $this->con->prepare(
                "INSERT INTO cliente (nome, tipo, cpf_cnpj, telefone, email, usuario_id)
                 VALUES (:nome, :tipo, :cpf_cnpj, :telefone, :email, :usuario)"
            );
            $this->bindDados($stmt, $c);
            $stmt->bindValue(":usuario", $c->getUsuarioId());
            $stmt->execute();
            return $this->con->lastInsertId();
        } catch (PDOException $ex) {
            echo "Erro ao cadastrar cliente: " . $ex->getMessage();
            return 0;
        }
    }

    public function alterar(Cliente $c) {
        try {
            $stmt = $this->con->prepare(
                "UPDATE cliente SET nome = :nome, tipo = :tipo, cpf_cnpj = :cpf_cnpj, telefone = :telefone, email = :email
                  WHERE id = :id"
            );
            $this->bindDados($stmt, $c);
            $stmt->bindValue(":id", $c->getId());
            return $stmt->execute();
        } catch (PDOException $ex) {
            echo "Erro ao alterar cliente: " . $ex->getMessage();
            return false;
        }
    }

    // Liga um cliente do balcão à conta que ele criou no site
    public function vincularUsuario($clienteId, $usuarioId) {
        $stmt = $this->con->prepare("UPDATE cliente SET usuario_id = :usuario WHERE id = :id");
        $stmt->bindValue(":usuario", $usuarioId);
        $stmt->bindValue(":id",      $clienteId);
        return $stmt->execute();
    }

    /* -------------------------------------------------------------------
     *  excluir() — devolve null se excluiu, ou a mensagem de erro.
     *  Não exclui cliente que já fez pedido (perderia o histórico de
     *  vendas) nem cliente com conta no site (o login ficaria sem cliente).
     * ------------------------------------------------------------------- */
    public function excluir(Cliente $c) {
        try {
            if ($c->getUsuarioId() != null) {
                return "Este cliente tem conta no site e não pode ser excluído. Peça ao gerente para desativar o usuário dele.";
            }

            $stmt = $this->con->prepare("SELECT COUNT(*) AS total FROM pedido WHERE cliente_id = :id");
            $stmt->bindValue(":id", $c->getId());
            $stmt->execute();
            $linha = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($linha["total"] > 0) {
                return "Este cliente já fez pedidos e não pode ser excluído.";
            }

            $stmt = $this->con->prepare("DELETE FROM cliente WHERE id = :id");
            $stmt->bindValue(":id", $c->getId());
            $stmt->execute();
            return null;
        } catch (PDOException $ex) {
            return "Erro ao excluir cliente: " . $ex->getMessage();
        }
    }

    // $busca: texto procurado no nome, CPF/CNPJ ou telefone (vazio = todos)
    public function listar($busca = "") {
        try {
            if ($busca == "") {
                $stmt = $this->con->query("SELECT * FROM cliente ORDER BY nome");
            } else {
                $stmt = $this->con->prepare(
                    "SELECT * FROM cliente
                      WHERE CONCAT(nome, ' ', IFNULL(cpf_cnpj, ''), ' ', telefone) LIKE :busca
                      ORDER BY nome"
                );
                $stmt->bindValue(":busca", "%" . $busca . "%");
                $stmt->execute();
            }

            $lista = array();
            while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $lista[] = $this->montar($linha);
            }
            return $lista;
        } catch (PDOException $ex) {
            echo "Erro ao listar clientes: " . $ex->getMessage();
            return array();
        }
    }

    public function exibir($id) {
        return $this->buscarUm("SELECT * FROM cliente WHERE id = :valor", $id);
    }

    public function buscarPorCpfCnpj($cpfCnpj) {
        return $this->buscarUm("SELECT * FROM cliente WHERE cpf_cnpj = :valor", $cpfCnpj);
    }

    // Cliente ligado ao usuário logado (usado em "Meus pedidos")
    public function buscarPorUsuario($usuarioId) {
        return $this->buscarUm("SELECT * FROM cliente WHERE usuario_id = :valor", $usuarioId);
    }

    // true se já existe OUTRO cliente com esse CPF/CNPJ
    public function cpfCnpjEmUso($cpfCnpj, $idIgnorado = 0) {
        if ($cpfCnpj == "") {
            return false;
        }
        $stmt = $this->con->prepare("SELECT id FROM cliente WHERE cpf_cnpj = :cpf_cnpj AND id <> :id");
        $stmt->bindValue(":cpf_cnpj", $cpfCnpj);
        $stmt->bindValue(":id",  $idIgnorado);
        $stmt->execute();
        return $stmt->fetch() !== false;
    }

    private function buscarUm($sql, $valor) {
        try {
            $stmt = $this->con->prepare($sql);
            $stmt->bindValue(":valor", $valor);
            $stmt->execute();
            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $linha ? $this->montar($linha) : null;
        } catch (PDOException $ex) {
            echo "Erro ao buscar cliente: " . $ex->getMessage();
            return null;
        }
    }

    private function bindDados($stmt, Cliente $c) {
        $stmt->bindValue(":nome",     $c->getNome());
        $stmt->bindValue(":tipo",     $c->getTipo());
        $stmt->bindValue(":cpf_cnpj", $c->getCpfCnpj() == "" ? null : $c->getCpfCnpj());
        $stmt->bindValue(":telefone", $c->getTelefone());
        $stmt->bindValue(":email",    $c->getEmail() == "" ? null : $c->getEmail());
    }

    private function montar($linha) {
        $c = new Cliente();
        $c->setId($linha["id"]);
        $c->setNome($linha["nome"]);
        $c->setTipo($linha["tipo"]);
        $c->setCpfCnpj($linha["cpf_cnpj"]);
        $c->setTelefone($linha["telefone"]);
        $c->setEmail($linha["email"]);
        $c->setUsuarioId($linha["usuario_id"]);
        return $c;
    }
}
