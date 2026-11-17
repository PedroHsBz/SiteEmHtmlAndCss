<?php
/* ==========================================================================
 *  Produto.php
 *  Camada: MODELO -> Entidade
 *
 *  Trator ou implemento vendido pela loja.
 * ========================================================================== */

class Produto {

    private $id;
    private $categoria;
    private $marca;
    private $modelo;
    private $condicao;
    private $ano;
    private $horas;
    private $preco;
    private $imagem;
    private $descricao;
    private $estoque;
    private $ativo;

    public function getId()                  { return $this->id; }
    public function setId($id)               { $this->id = $id; }

    public function getCategoria()           { return $this->categoria; }
    public function setCategoria($categoria) { $this->categoria = $categoria; }

    public function getMarca()               { return $this->marca; }
    public function setMarca($marca)         { $this->marca = $marca; }

    public function getModelo()              { return $this->modelo; }
    public function setModelo($modelo)       { $this->modelo = $modelo; }

    public function getCondicao()            { return $this->condicao; }
    public function setCondicao($condicao)   { $this->condicao = $condicao; }

    public function getAno()                 { return $this->ano; }
    public function setAno($ano)             { $this->ano = $ano; }

    public function getHoras()               { return $this->horas; }
    public function setHoras($horas)         { $this->horas = $horas; }

    public function getPreco()               { return $this->preco; }
    public function setPreco($preco)         { $this->preco = $preco; }

    public function getImagem()              { return $this->imagem; }
    public function setImagem($imagem)       { $this->imagem = $imagem; }

    public function getDescricao()           { return $this->descricao; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }

    public function getEstoque()             { return $this->estoque; }
    public function setEstoque($estoque)     { $this->estoque = $estoque; }

    public function getAtivo()               { return $this->ativo; }
    public function setAtivo($ativo)         { $this->ativo = $ativo; }

    // Marca + modelo, usado em várias telas (ex.: "John Deere 5075E")
    public function getNome()                { return $this->marca . " " . $this->modelo; }
}
