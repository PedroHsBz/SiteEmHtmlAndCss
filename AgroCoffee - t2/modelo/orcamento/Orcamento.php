<?php
/* ==========================================================================
 *  Orcamento.php
 *  Camada: MODELO -> Entidade
 *
 *  Orçamento de reposição de estoque, feito com um fornecedor.
 *  status: Pendente -> Aprovado | Rejeitado
 *          Aprovado -> Concluído (quando o financeiro paga a conta)
 * ========================================================================== */

class Orcamento {

    private $id;
    private $fornecedorId;
    private $fornecedorNome;   // vem do JOIN com fornecedor, só para exibir
    private $produtoId;
    private $produtoNome;   // vem do JOIN com produto, só para exibir
    private $quantidade;
    private $valorUnitario;
    private $vencimento;
    private $status;
    private $motivoRejeicao;
    private $data;

    public function getId()                          { return $this->id; }
    public function setId($id)                       { $this->id = $id; }

    public function getFornecedorId()                { return $this->fornecedorId; }
    public function setFornecedorId($fornecedorId)   { $this->fornecedorId = $fornecedorId; }

    public function getFornecedorNome()              { return $this->fornecedorNome; }
    public function setFornecedorNome($nome)         { $this->fornecedorNome = $nome; }

    public function getProdutoId()                   { return $this->produtoId; }
    public function setProdutoId($produtoId)         { $this->produtoId = $produtoId; }

    public function getProdutoNome()                 { return $this->produtoNome; }
    public function setProdutoNome($produtoNome)     { $this->produtoNome = $produtoNome; }

    public function getQuantidade()                  { return $this->quantidade; }
    public function setQuantidade($quantidade)       { $this->quantidade = $quantidade; }

    public function getValorUnitario()               { return $this->valorUnitario; }
    public function setValorUnitario($valor)         { $this->valorUnitario = $valor; }

    public function getVencimento()                  { return $this->vencimento; }
    public function setVencimento($vencimento)       { $this->vencimento = $vencimento; }

    public function getStatus()                      { return $this->status; }
    public function setStatus($status)               { $this->status = $status; }

    public function getMotivoRejeicao()              { return $this->motivoRejeicao; }
    public function setMotivoRejeicao($motivo)       { $this->motivoRejeicao = $motivo; }

    public function getData()                        { return $this->data; }
    public function setData($data)                   { $this->data = $data; }

    // Quantidade x valor unitário
    public function getTotal()                       { return $this->quantidade * $this->valorUnitario; }
}
