<?php
/* ==========================================================================
 *  FornecedorDAO.php
 *  Camada: MODELO -> DAO
 *
 *  Operações no banco da tabela fornecedor.
 *  CNPJ e e-mail são opcionais: campo vazio é gravado como NULL.
 * ========================================================================== */

require_once __DIR__ . "/../ConnectionFactory.php";
require_once __DIR__ . "/../Busca.php";
require_once __DIR__ . "/Fornecedor.php";

class FornecedorDAO {

    private $con;

    public function __construct() {
        $this->con = ConnectionFactory::getConnection();
    }

    public function cadastrar(Fornecedor $f) {
        try {
            $stmt = $this->con->prepare(
                "INSERT INTO fornecedor (nome, cnpj, telefone, email, ativo)
                 VALUES (:nome, :cnpj, :telefone, :email, :ativo)"
            );
            $this->bindDados($stmt, $f);
            return $stmt->execute();
        } catch (PDOException $ex) {
            echo "Erro ao cadastrar fornecedor: " . $ex->getMessage();
            return false;
        }
    }

    public function alterar(Fornecedor $f) {
        try {
            $stmt = $this->con->prepare(
                "UPDATE fornecedor SET nome = :nome, cnpj = :cnpj, telefone = :telefone,
                        email = :email, ativo = :ativo
                  WHERE id = :id"
            );
            $this->bindDados($stmt, $f);
            $stmt->bindValue(":id", $f->getId());
            return $stmt->execute();
        } catch (PDOException $ex) {
            echo "Erro ao alterar fornecedor: " . $ex->getMessage();
            return false;
        }
    }

    // $somenteAtivos = true para o <select> do orçamento
    // Campos que podem ser escolhidos no "Buscar em" (ver modelo/Busca.php)
    public static function camposBusca() {
        return array(
            "id"       => array("Código",   "id"),
            "nome"     => array("Nome",     "nome"),
            "cnpj"     => array("CNPJ",     "cnpj"),
            "telefone" => array("Telefone", "telefone"),
            "email"    => array("E-mail",   "email"),
            "situacao" => array("Situação (Ativo/Inativo)", "IF(ativo = 1, 'Ativo', 'Inativo')"),
        );
    }

    // $busca: texto procurado (vazio = todos)
    // $campo: em qual campo procurar (vazio = em todos)
    public function listar($somenteAtivos, $busca = "", $campo = "") {
        try {
            $sql = "SELECT * FROM fornecedor WHERE 1 = 1";
            if ($somenteAtivos) {
                $sql .= " AND ativo = 1";
            }
            if ($busca != "") {
                $sql .= " AND " . sqlBusca(self::camposBusca(), $campo);
            }
            $sql .= " ORDER BY nome";

            $stmt = $this->con->prepare($sql);
            if ($busca != "") {
                $stmt->bindValue(":busca", "%" . $busca . "%");
            }
            $stmt->execute();

            $lista = array();
            while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $lista[] = $this->montar($linha);
            }
            return $lista;
        } catch (PDOException $ex) {
            echo "Erro ao listar fornecedores: " . $ex->getMessage();
            return array();
        }
    }

    public function exibir($id) {
        try {
            $stmt = $this->con->prepare("SELECT * FROM fornecedor WHERE id = :id");
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $linha ? $this->montar($linha) : null;
        } catch (PDOException $ex) {
            echo "Erro ao exibir fornecedor: " . $ex->getMessage();
            return null;
        }
    }

    // true se já existe OUTRO fornecedor com esse CNPJ
    public function cnpjEmUso($cnpj, $idIgnorado = 0) {
        if ($cnpj == "") {
            return false;
        }
        $stmt = $this->con->prepare("SELECT id FROM fornecedor WHERE cnpj = :cnpj AND id <> :id");
        $stmt->bindValue(":cnpj", $cnpj);
        $stmt->bindValue(":id",   $idIgnorado);
        $stmt->execute();
        return $stmt->fetch() !== false;
    }

    private function bindDados($stmt, Fornecedor $f) {
        $stmt->bindValue(":nome",     $f->getNome());
        $stmt->bindValue(":cnpj",     $f->getCnpj()  == "" ? null : $f->getCnpj());
        $stmt->bindValue(":telefone", $f->getTelefone());
        $stmt->bindValue(":email",    $f->getEmail() == "" ? null : $f->getEmail());
        $stmt->bindValue(":ativo",    $f->getAtivo());
    }

    private function montar($linha) {
        $f = new Fornecedor();
        $f->setId($linha["id"]);
        $f->setNome($linha["nome"]);
        $f->setCnpj($linha["cnpj"]);
        $f->setTelefone($linha["telefone"]);
        $f->setEmail($linha["email"]);
        $f->setAtivo($linha["ativo"]);
        return $f;
    }
}
