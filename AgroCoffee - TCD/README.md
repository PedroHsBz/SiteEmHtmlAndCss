# AgroCoffee — Site em PHP no padrão MVC

Loja de tratores e implementos em PHP puro, organizada em MVC no mesmo
padrão da agenda (roteador com `?fun=`, controle, DAO e entidade).

## Como rodar

1. Importe `banco/agrocoffee.sql` no phpMyAdmin (aba Importar).
2. Abra `http://localhost/SiteEmHtmlOrCss/AgroCoffee/`.

Usuários de teste (senha de todos: `agro123`):

| E-mail | Perfil |
|---|---|
| gerente@agrocoffee.test | gerente |
| vendedor@agrocoffee.test | vendedor |
| financeiro@agrocoffee.test | financeiro |
| cliente@agrocoffee.test | cliente |

## O que cada perfil faz

- **Cliente**: se cadastra, faz pedido pela página do produto, vê e cancela os próprios pedidos pendentes.
- **Vendedor**: vê os pedidos, registra pedido de WhatsApp/balcão, conclui ou cancela, cadastra produtos, mexe no estoque e registra orçamento de reposição.
- **Financeiro**: contas a pagar e a receber, cadastra conta avulsa, registra pagamento e recebimento.
- **Gerente**: tudo acima, mais aprovar/rejeitar orçamentos e cadastrar usuários.

## Regras principais

- Pedido: `Pendente` → `Concluído` ou `Cancelado`.
- Concluir o pedido baixa o estoque e gera a conta a receber (`PedidoDAO::concluir`, com transação).
- Orçamento: `Pendente` → `Aprovado` ou `Rejeitado`. Aprovar gera a conta a pagar (`OrcamentoDAO::aprovar`).
- Pagar a conta de um orçamento dá entrada no estoque e muda o orçamento para `Concluído` (`ContaDAO::pagar`).
- Toda mudança de estoque fica registrada em `estoque_movimento` (`ProdutoDAO::movimentar`).

## Estrutura

```
AgroCoffee/
├── index.php, pagina.php        ← páginas do site
├── produto.php, usuario.php,
│   pedido.php, orcamento.php,
│   conta.php                    ← roteadores (?fun=...)
├── controle/
│   ├── Acesso.php               ← usuarioLogado() e temAcesso()
│   ├── usuario/  produto/  pedido/  orcamento/  conta/
├── modelo/
│   ├── ConnectionFactory.php
│   ├── usuario/   Usuario.php    UsuarioDAO.php
│   ├── produto/   Produto.php    ProdutoDAO.php
│   ├── pedido/    Pedido.php     PedidoDAO.php
│   ├── orcamento/ Orcamento.php  OrcamentoDAO.php
│   └── conta/     Conta.php      ContaDAO.php
├── visao/
│   ├── layout/  (topo, base, mensagem, semAcesso)
│   └── pagina/  usuario/  produto/  pedido/  orcamento/  conta/
├── assets/css/style.css
└── banco/agrocoffee.sql
```
