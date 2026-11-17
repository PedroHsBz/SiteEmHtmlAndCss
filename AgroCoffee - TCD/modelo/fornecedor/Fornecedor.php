<?php
/* ==========================================================================
 *  Fornecedor.php
 *  Camada: MODELO -> Entidade
 *
 *  Empresa de quem a loja compra para repor o estoque.
 * ========================================================================== */

class Fornecedor {

    private $id;
    private $nome;
    private $cnpj;
    private $telefone;
    private $email;
    private $ativo;

    public function getId()                { return $this->id; }
    public function setId($id)             { $this->id = $id; }

    public function getNome()              { return $this->nome; }
    public function setNome($nome)         { $this->nome = $nome; }

    public function getCnpj()              { return $this->cnpj; }
    public function setCnpj($cnpj)         { $this->cnpj = $cnpj; }

    public function getTelefone()          { return $this->telefone; }
    public function setTelefone($telefone) { $this->telefone = $telefone; }

    public function getEmail()             { return $this->email; }
    public function setEmail($email)       { $this->email = $email; }

    public function getAtivo()             { return $this->ativo; }
    public function setAtivo($ativo)       { $this->ativo = $ativo; }
}
