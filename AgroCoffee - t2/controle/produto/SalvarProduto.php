<?php
/* ==========================================================================
 *  SalvarProduto.php
 *  Camada: CONTROLE
 *
 *  Cadastra um produto novo ou altera um existente.
 *    produto.php?fun=salvar        -> novo
 *    produto.php?fun=salvar&id=3   -> alterar o produto 3
 *  A foto é enviada por upload (igual ao cadastro de contato da agenda).
 * ========================================================================== */

require_once __DIR__ . "/../../modelo/produto/ProdutoDAO.php";

class SalvarProduto {

    public function __construct() {

        if (!temAcesso(array("vendedor", "financeiro", "gerente"))) {
            require __DIR__ . "/../../visao/layout/semAcesso.php";
            return;
        }

        $dao = new ProdutoDAO();

        if (!isset($_POST["enviar"])) {
            $p = isset($_GET["id"]) ? $dao->exibir($_GET["id"]) : new Produto();
            require __DIR__ . "/../../visao/produto/form.php";
            return;
        }

        $p = new Produto();
        $p->setId($_POST["id"]);
        $p->setCategoria($_POST["categoria"]);
        $p->setMarca($_POST["marca"]);
        $p->setModelo($_POST["modelo"]);
        $p->setCondicao($_POST["condicao"]);
        $p->setAno($_POST["ano"]);
        $p->setHoras($_POST["horas"]);
        $p->setPreco($_POST["preco"]);
        $p->setDescricao($_POST["descricao"]);
        $p->setAtivo(isset($_POST["ativo"]) ? 1 : 0);
        $p->setImagem($_POST["imagemAtual"]);   // mantém a foto antiga se não enviar outra

        /* ----------- Upload da foto ----------- */
        if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] === UPLOAD_ERR_OK) {
            $extensao = strtolower(pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION));

            if (in_array($extensao, array("jpg", "jpeg", "png", "webp"))) {
                $novoNome = uniqid("produto-") . "." . $extensao;
                $destino  = __DIR__ . "/../../assets/img/produtos/" . $novoNome;

                if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $destino)) {
                    $p->setImagem("assets/img/produtos/" . $novoNome);
                }
            } else {
                $erro = "A foto precisa ser JPG, PNG ou WEBP.";
            }
        }

        if (trim($p->getMarca()) == "" || trim($p->getModelo()) == "") {
            $erro = "Informe a marca e o modelo.";
        } elseif ($p->getPreco() <= 0) {
            $erro = "Informe um preço maior que zero.";
        } elseif ($p->getImagem() == "") {
            $erro = "Envie uma foto do produto.";
        }

        if (isset($erro)) {
            require __DIR__ . "/../../visao/produto/form.php";
            return;
        }

        if ($p->getId() == "") {
            $dao->cadastrar($p);
            $status = "Produto " . $p->getNome() . " cadastrado. Lembre de dar entrada no estoque.";
        } else {
            $dao->alterar($p);
            $status = "Produto " . $p->getNome() . " alterado.";
        }

        $lista = $dao->listar(false);
        require __DIR__ . "/../../visao/produto/admin.php";
    }
}
