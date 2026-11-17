<?php
/* ==========================================================================
 *  Usuario.php
 *  Camada: MODELO -> Entidade
 *
 *  Quem faz LOGIN no sistema.
 *  perfil: cliente | vendedor | financeiro | gerente
 *  (Os dados de compra do cliente, como CPF/CNPJ e telefone, ficam em Cliente.)
 * ========================================================================== */

class Usuario {

    private $id;
    private $nome;
    private $email;
    private $senha;
    private $perfil;
    private $ativo;

    public function getId()            { return $this->id; }
    public function setId($id)         { $this->id = $id; }

    public function getNome()          { return $this->nome; }
    public function setNome($nome)     { $this->nome = $nome; }

    public function getEmail()         { return $this->email; }
    public function setEmail($email)   { $this->email = $email; }

    public function getSenha()         { return $this->senha; }
    public function setSenha($senha)   { $this->senha = $senha; }

    public function getPerfil()        { return $this->perfil; }
    public function setPerfil($perfil) { $this->perfil = $perfil; }

    public function getAtivo()         { return $this->ativo; }
    public function setAtivo($ativo)   { $this->ativo = $ativo; }
}
