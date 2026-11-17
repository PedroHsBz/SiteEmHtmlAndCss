<?php
/* ==========================================================================
 *  Pedido.php
 *  Camada: MODELO -> Entidade
 *
 *  status: Pendente -> Concluído | Cancelado
 *  origem: Site | WhatsApp | Balcão
 *  encomenda: 1 quando foi pedido mais do que havia em estoque
 * ========================================================================== */

class Pedido {

    private $id;
    private $clienteId;
    private $clienteNome;       // vem do JOIN com cliente, só para exibir
    private $clienteTelefone;   // vem do JOIN com cliente, só para exibir
    private $clienteEmail;      // vem do JOIN com cliente, usado no e-mail de conclusão
    private $produtoId;
    private $produtoNome;       // vem do JOIN com produto, só para exibir
    private $quantidade;
    private $valor;
    private $origem;
    private $encomenda;         // 1 = pedido feito sem estoque, aguarda a reposição
    private $status;
    private $motivoCancelamento;
    private $data;

    public function getId()                         { return $this->id; }
    public function setId($id)                      { $this->id = $id; }

    public function getClienteId()                  { return $this->clienteId; }
    public function setClienteId($clienteId)        { $this->clienteId = $clienteId; }

    public function getClienteNome()                { return $this->clienteNome; }
    public function setClienteNome($nome)           { $this->clienteNome = $nome; }

    public function getClienteTelefone()            { return $this->clienteTelefone; }
    public function setClienteTelefone($telefone)   { $this->clienteTelefone = $telefone; }

    public function getClienteEmail()               { return $this->clienteEmail; }
    public function setClienteEmail($email)         { $this->clienteEmail = $email; }

    public function getProdutoId()                { return $this->produtoId; }
    public function setProdutoId($produtoId)        { $this->produtoId = $produtoId; }

    public function getProdutoNome()                { return $this->produtoNome; }
    public function setProdutoNome($produtoNome)    { $this->produtoNome = $produtoNome; }

    public function getQuantidade()                 { return $this->quantidade; }
    public function setQuantidade($quantidade)      { $this->quantidade = $quantidade; }

    public function getValor()                      { return $this->valor; }
    public function setValor($valor)                { $this->valor = $valor; }

    public function getOrigem()                     { return $this->origem; }
    public function setOrigem($origem)              { $this->origem = $origem; }

    public function getEncomenda()                  { return $this->encomenda; }
    public function setEncomenda($encomenda)        { $this->encomenda = $encomenda; }

    public function getStatus()                     { return $this->status; }
    public function setStatus($status)              { $this->status = $status; }

    public function getMotivoCancelamento()         { return $this->motivoCancelamento; }
    public function setMotivoCancelamento($motivo)  { $this->motivoCancelamento = $motivo; }

    public function getData()                       { return $this->data; }
    public function setData($data)                  { $this->data = $data; }
}
