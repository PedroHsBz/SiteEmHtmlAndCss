<?php
/* ==========================================================================
 *  Cliente.php
 *  Camada: MODELO -> Entidade
 *
 *  Quem compra na loja. Pode ter login (usuarioId preenchido, quando
 *  criou conta no site) ou não (cadastrado pelo vendedor no balcão).
 * ========================================================================== */

class Cliente {

    private $id;
    private $nome;
    private $tipo;        // Física | Jurídica
    private $cpfCnpj;
    private $telefone;
    private $email;
    private $usuarioId;

    public function getId()                  { return $this->id; }
    public function setId($id)               { $this->id = $id; }

    public function getNome()                { return $this->nome; }
    public function setNome($nome)           { $this->nome = $nome; }

    public function getTipo()                { return $this->tipo; }
    public function setTipo($tipo)           { $this->tipo = $tipo; }

    public function getCpfCnpj()             { return $this->cpfCnpj; }
    public function setCpfCnpj($cpfCnpj)     { $this->cpfCnpj = $cpfCnpj; }

    public function getTelefone()            { return $this->telefone; }
    public function setTelefone($telefone)   { $this->telefone = $telefone; }

    public function getEmail()               { return $this->email; }
    public function setEmail($email)         { $this->email = $email; }

    public function getUsuarioId()           { return $this->usuarioId; }
    public function setUsuarioId($usuarioId) { $this->usuarioId = $usuarioId; }
}
