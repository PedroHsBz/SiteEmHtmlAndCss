<?php
/* ==========================================================================
 *  Conta.php
 *  Camada: MODELO -> Entidade
 *
 *  tipo  : Pagar | Receber
 *  status: Aberta | Paga
 * ========================================================================== */

class Conta {

    // Juros simples cobrados do cliente por mês de atraso (contas a receber)
    const JUROS_AO_MES = 2;   // em %

    private $id;
    private $tipo;
    private $descricao;
    private $valor;
    private $vencimento;
    private $status;
    private $pagoEm;
    private $juros;          // juros cobrados no recebimento (fica gravado)
    private $pedidoId;
    private $numeroParcela;  // ex.: 2  (vazio quando é à vista ou avulsa)
    private $totalParcelas;  // ex.: 3
    private $orcamentoId;

    public function getId()                        { return $this->id; }
    public function setId($id)                     { $this->id = $id; }

    public function getTipo()                      { return $this->tipo; }
    public function setTipo($tipo)                 { $this->tipo = $tipo; }

    public function getDescricao()                 { return $this->descricao; }
    public function setDescricao($descricao)       { $this->descricao = $descricao; }

    public function getValor()                     { return $this->valor; }
    public function setValor($valor)               { $this->valor = $valor; }

    public function getVencimento()                { return $this->vencimento; }
    public function setVencimento($vencimento)     { $this->vencimento = $vencimento; }

    public function getStatus()                    { return $this->status; }
    public function setStatus($status)             { $this->status = $status; }

    public function getPagoEm()                    { return $this->pagoEm; }
    public function setPagoEm($pagoEm)             { $this->pagoEm = $pagoEm; }

    public function getJuros()                     { return $this->juros; }
    public function setJuros($juros)               { $this->juros = $juros; }

    public function getPedidoId()                  { return $this->pedidoId; }
    public function setPedidoId($pedidoId)         { $this->pedidoId = $pedidoId; }

    public function getNumeroParcela()             { return $this->numeroParcela; }
    public function setNumeroParcela($numero)      { $this->numeroParcela = $numero; }

    public function getTotalParcelas()             { return $this->totalParcelas; }
    public function setTotalParcelas($total)       { $this->totalParcelas = $total; }

    public function getOrcamentoId()             { return $this->orcamentoId; }
    public function setOrcamentoId($orcamentoId)   { $this->orcamentoId = $orcamentoId; }

    /* -------------------------------------------------------------------
     *  Juros por mês de atraso (só conta a RECEBER, que é cobrada do cliente)
     *  Cada mês começado conta inteiro: 1 a 30 dias = 1 mês, 31 a 60 = 2...
     *  $data: dia usado no cálculo (hoje, ou o dia em que o cliente pagou)
     * ------------------------------------------------------------------- */
    public function calcularMesesAtraso($data) {
        if ($this->tipo != "Receber" || $this->vencimento == null || $data <= $this->vencimento) {
            return 0;
        }
        $dias = (strtotime($data) - strtotime($this->vencimento)) / 86400;
        return (int) ceil($dias / 30);
    }

    public function calcularJuros($data) {
        $meses = $this->calcularMesesAtraso($data);
        return round($this->valor * self::JUROS_AO_MES / 100 * $meses, 2);
    }
}
