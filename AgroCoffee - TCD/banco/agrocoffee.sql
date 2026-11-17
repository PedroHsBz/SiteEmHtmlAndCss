-- =====================================================
-- AgroCoffee - Banco de dados
-- Importe este arquivo no phpMyAdmin (aba "Importar").
-- ATENÇÃO: apaga e recria o banco agrocoffee.
--
-- Usuários de teste (senha de todos: agro123)
--   gerente@agrocoffee.test
--   vendedor@agrocoffee.test
--   financeiro@agrocoffee.test
--   cliente@agrocoffee.test
-- =====================================================

DROP DATABASE IF EXISTS agrocoffee;
CREATE DATABASE agrocoffee CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agrocoffee;

-- -----------------------------------------------------
-- Usuários: quem faz LOGIN no sistema
--   perfil: cliente | vendedor | financeiro | gerente
--   ativo : 1 = pode entrar no sistema; 0 = desativado
-- -----------------------------------------------------
CREATE TABLE usuario (
    id     INT          NOT NULL AUTO_INCREMENT,
    nome   VARCHAR(100) NOT NULL,
    email  VARCHAR(100) NOT NULL UNIQUE,
    senha  VARCHAR(255) NOT NULL,
    perfil VARCHAR(20)  NOT NULL DEFAULT 'cliente',
    ativo  TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Clientes: quem COMPRA
--   Cadastrado pelo vendedor (balcão / WhatsApp) ou
--   automaticamente quando a pessoa cria conta no site.
--   tipo      : Física (CPF) ou Jurídica (CNPJ)
--   usuario_id: preenchido só se o cliente tem login
-- -----------------------------------------------------
CREATE TABLE cliente (
    id         INT          NOT NULL AUTO_INCREMENT,
    nome       VARCHAR(100) NOT NULL,
    tipo       VARCHAR(10)  NOT NULL DEFAULT 'Física',   -- Física | Jurídica
    cpf_cnpj   VARCHAR(18)  NULL UNIQUE,   -- pessoa (CPF) ou empresa (CNPJ)
    telefone   VARCHAR(20)  NOT NULL,
    email      VARCHAR(100) NULL,
    usuario_id INT          NULL UNIQUE,
    PRIMARY KEY (id),
    FOREIGN KEY (usuario_id) REFERENCES usuario (id)
);

-- -----------------------------------------------------
-- Fornecedores (só cadastro, não fazem login)
-- -----------------------------------------------------
CREATE TABLE fornecedor (
    id       INT          NOT NULL AUTO_INCREMENT,
    nome     VARCHAR(120) NOT NULL,
    cnpj     VARCHAR(18)  NULL UNIQUE,
    telefone VARCHAR(20)  NOT NULL,
    email    VARCHAR(100) NULL,
    ativo    TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Produtos (tratores e implementos)
-- -----------------------------------------------------
CREATE TABLE produto (
    id        INT           NOT NULL AUTO_INCREMENT,
    categoria VARCHAR(50)   NOT NULL,
    marca     VARCHAR(50)   NOT NULL,
    modelo    VARCHAR(100)  NOT NULL,
    condicao  VARCHAR(20)   NOT NULL,
    ano       INT           NOT NULL,
    horas     INT           NOT NULL DEFAULT 0,
    preco     DECIMAL(12,2) NOT NULL,
    imagem    VARCHAR(255)  NOT NULL,
    descricao TEXT,
    estoque   INT           NOT NULL DEFAULT 0,
    ativo     TINYINT(1)    NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Movimentações de estoque
--   tipo : Entrada | Saída
--   saldo: estoque do produto depois da movimentação
-- -----------------------------------------------------
CREATE TABLE estoque_movimento (
    id         INT          NOT NULL AUTO_INCREMENT,
    produto_id INT          NOT NULL,
    tipo       VARCHAR(10)  NOT NULL,
    quantidade INT          NOT NULL,
    saldo      INT          NOT NULL,
    motivo     VARCHAR(150) NOT NULL,
    data       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (produto_id) REFERENCES produto (id)
);

-- -----------------------------------------------------
-- Pedidos
--   status: Pendente -> Concluído | Cancelado
--   origem: Site | WhatsApp | Balcão
--   encomenda: 1 quando a quantidade passa do estoque
-- -----------------------------------------------------
CREATE TABLE pedido (
    id                  INT           NOT NULL AUTO_INCREMENT,
    cliente_id          INT           NOT NULL,
    produto_id          INT           NOT NULL,
    quantidade          INT           NOT NULL DEFAULT 1,
    valor               DECIMAL(12,2) NOT NULL,
    origem              VARCHAR(20)   NOT NULL DEFAULT 'Site',
    encomenda           BOOLEAN       NOT NULL DEFAULT FALSE,   -- TRUE = pedido feito sem estoque, aguarda reposição
    status              VARCHAR(20)   NOT NULL DEFAULT 'Pendente',
    motivo_cancelamento VARCHAR(255)  NULL,
    data_pedido         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (cliente_id) REFERENCES cliente (id),
    FOREIGN KEY (produto_id) REFERENCES produto (id)
);

-- -----------------------------------------------------
-- Orçamentos de reposição de estoque
--   status: Pendente -> Aprovado | Rejeitado
--           Aprovado -> Concluído (quando o financeiro paga)
-- -----------------------------------------------------
CREATE TABLE orcamento (
    id              INT           NOT NULL AUTO_INCREMENT,
    fornecedor_id   INT           NOT NULL,
    produto_id      INT           NOT NULL,
    quantidade      INT           NOT NULL,
    valor_unitario  DECIMAL(12,2) NOT NULL,
    vencimento      DATE          NULL,   -- vazio = paga quando o produto chegar
    status          VARCHAR(20)   NOT NULL DEFAULT 'Pendente',
    motivo_rejeicao VARCHAR(255)  NULL,
    data_criacao    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (fornecedor_id) REFERENCES fornecedor (id),
    FOREIGN KEY (produto_id)    REFERENCES produto (id)
);

-- -----------------------------------------------------
-- Contas a pagar e a receber
--   tipo  : Pagar | Receber
--   status: Aberta | Paga
--   pedido_id   : conta a receber gerada pela venda
--   orcamento_id: conta a pagar gerada pelo orçamento aprovado
--   juros       : 2% ao mês de atraso, só nas contas a receber
-- -----------------------------------------------------
CREATE TABLE conta (
    id           INT           NOT NULL AUTO_INCREMENT,
    tipo         VARCHAR(10)   NOT NULL,
    descricao    VARCHAR(150)  NOT NULL,
    valor        DECIMAL(12,2) NOT NULL,
    vencimento   DATE          NULL,   -- vazio = sem vencimento (paga na entrega)
    status       VARCHAR(10)   NOT NULL DEFAULT 'Aberta',
    pago_em      DATE          NULL,
    juros        DECIMAL(12,2) NOT NULL DEFAULT 0,   -- juros de atraso cobrados no recebimento
    pedido_id    INT           NULL,
    numero_parcela INT         NULL,   -- ex.: 2 (vazio = à vista ou avulsa)
    total_parcelas INT         NULL,   -- ex.: 3
    orcamento_id INT           NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (pedido_id)    REFERENCES pedido (id),
    FOREIGN KEY (orcamento_id) REFERENCES orcamento (id)
);

-- -----------------------------------------------------
-- Dados
-- -----------------------------------------------------
INSERT INTO usuario (id, nome, email, senha, perfil) VALUES
(1, 'Gerente AgroCoffee', 'gerente@agrocoffee.test',    '$2y$10$AgWp/lh4rIkO.oRqLZhtceiwItpQPm0nIh2bBLAF4.nbY3sCBs85m', 'gerente'),
(2, 'Vitor Vendedor',    'vendedor@agrocoffee.test',   '$2y$10$AgWp/lh4rIkO.oRqLZhtceiwItpQPm0nIh2bBLAF4.nbY3sCBs85m', 'vendedor'),
(3, 'Fábio Financeiro',  'financeiro@agrocoffee.test', '$2y$10$AgWp/lh4rIkO.oRqLZhtceiwItpQPm0nIh2bBLAF4.nbY3sCBs85m', 'financeiro'),
(4, 'Carlos Cliente',    'cliente@agrocoffee.test',    '$2y$10$AgWp/lh4rIkO.oRqLZhtceiwItpQPm0nIh2bBLAF4.nbY3sCBs85m', 'cliente');

INSERT INTO cliente (nome, tipo, cpf_cnpj, telefone, email, usuario_id) VALUES
('Carlos Cliente',     'Física',   '529.982.247-25', '(34) 99999-0000', 'cliente@agrocoffee.test', 4),
('José da Fazenda',    'Física',   NULL,             '(34) 98888-1111', NULL,                      NULL),
('Fazenda Boa Vista Ltda', 'Jurídica', '45.997.418/0001-53', '(34) 3851-2200', 'compras@boavista.test', NULL);

INSERT INTO fornecedor (nome, cnpj, telefone, email) VALUES
('Agro Máquinas Cerrado Ltda',  '11.222.333/0001-81', '(34) 3831-0000', 'vendas@agromaquinas.test'),
('Implementos Alto Paranaíba',  '12.345.678/0001-95', '(34) 3842-0000', 'comercial@implementosap.test');

INSERT INTO produto (categoria, marca, modelo, condicao, ano, horas, preco, imagem, descricao, estoque) VALUES
('Tratores',    'John Deere', '5075E',                   'Novo',     2025, 0,    289900, 'assets/img/card1.jpg',    'Trator compacto e econômico, ideal para pequenas e médias propriedades.', 3),
('Tratores',    'John Deere', '6125J',                   'Novo',     2025, 0,    598000, 'assets/img/headline.jpg', 'Versátil e robusto, com cabine climatizada para longas jornadas.', 2),
('Tratores',    'John Deere', '6155J',                   'Novo',     2025, 0,    742000, 'assets/img/sobre2.jpg',   'Alta potência com baixo consumo, para implementos pesados.', 1),
('Tratores',    'John Deere', '7230J',                   'Novo',     2025, 0,    915000, 'assets/img/sobre1.jpg',   'Piloto automático de fábrica, para máxima precisão no plantio.', 1),
('Tratores',    'Case IH',    'Magnum 340',              'Novo',     2024, 0,    1250000,'assets/img/sobre.jpg',    'Transmissão CVT, feito para grandes áreas.', 0),
('Tratores',    'John Deere', '6110J',                   'Seminovo', 2021, 2300, 389000, 'assets/img/card3.jpg',    'Seminovo revisado, único dono, manutenções em dia.', 1),
('Implementos', 'Baldan',     'Grade Aradora 14 discos', 'Novo',     2025, 0,    68500,  'assets/img/card2.jpg',    'Grade aradora para preparo primário do solo.', 6);

INSERT INTO estoque_movimento (produto_id, tipo, quantidade, saldo, motivo)
SELECT id, 'Entrada', estoque, estoque, 'Estoque inicial' FROM produto WHERE estoque > 0;

INSERT INTO conta (tipo, descricao, valor, vencimento) VALUES
('Pagar',   'Aluguel do galpão', 8500.00, CURDATE() + INTERVAL 2 DAY),
('Pagar',   'Conta de energia',  1890.40, CURDATE() - INTERVAL 1 DAY),
('Receber', 'Revisão de trator - José da Fazenda', 4200.00, CURDATE() - INTERVAL 45 DAY);   -- vencida há 45 dias = 2 meses de juros
