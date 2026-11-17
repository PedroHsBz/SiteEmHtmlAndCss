<?php
/* ==========================================================================
 *  Email.php
 *  Camada: MODELO (serviço)
 *
 *  Envia o e-mail de "pedido concluído" pelo Gmail, usando a biblioteca
 *  PHPMailer.
 *
 *  Para funcionar:
 *    1. Baixe o PHPMailer em github.com/PHPMailer/PHPMailer (Code > Download ZIP)
 *       e copie a pasta "src" para  lib/PHPMailer/src/
 *    2. Copie config.local.exemplo.php para config.local.php e preencha
 *       com o Gmail e a SENHA DE APP (não é a senha normal do Gmail).
 * ========================================================================== */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class Email
{

    /* -------------------------------------------------------------------
     *  enviarPedidoConcluido() — devolve null se enviou, ou o motivo
     *  de não ter enviado.
     * ------------------------------------------------------------------- */
    public static function enviarPedidoConcluido(Pedido $p)
    {

        if ($p->getClienteEmail() == "") {
            return "o cliente não tem e-mail cadastrado.";
        }

        /* ----------- 1. Carrega a biblioteca e a configuração ----------- */
        $pasta = __DIR__ . "/../lib/PHPMailer/src/";
        if (!file_exists($pasta . "PHPMailer.php")) {
            return "o PHPMailer não está instalado (veja modelo/Email.php).";
        }
        require_once $pasta . "Exception.php";
        require_once $pasta . "PHPMailer.php";
        require_once $pasta . "SMTP.php";

        $config = __DIR__ . "/../config.local.php";
        if (!file_exists($config)) {
            return "o e-mail da loja não foi configurado (crie o config.local.php).";
        }
        require_once $config;

        /* ----------- 2. Monta a mensagem ----------- */
        $nome  = htmlspecialchars($p->getClienteNome());
        $texto = "<p>Olá, " . $nome . "!</p>"
            . "<p>Seu pedido <strong>#" . $p->getId() . "</strong> foi <strong>concluído</strong>.</p>"
            . "<p>Produto: " . htmlspecialchars($p->getProdutoNome()) . "<br>"
            . "Quantidade: " . $p->getQuantidade() . "<br>"
            . "Valor: R$ " . number_format($p->getValor(), 2, ",", ".") . "</p>"
            . "<p>Obrigado pela compra! Qualquer dúvida, fale com a gente pelo WhatsApp.</p>"
            . "<p>AgroCoffee</p>";

        /* ----------- 3. Envia pelo Gmail ----------- */
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = "smtp.gmail.com";
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USUARIO;
            $mail->Password   = SMTP_SENHA_APP;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = "UTF-8";

            $mail->setFrom(SMTP_USUARIO, "AgroCoffee");
            $mail->addAddress($p->getClienteEmail(), $p->getClienteNome());

            $mail->isHTML(true);
            $mail->Subject = "Pedido #" . $p->getId() . " concluído - AgroCoffee";
            $mail->Body    = $texto;
            $mail->AltBody = strip_tags(str_replace(array("<br>", "</p>"), "\n", $texto));

            $mail->send();
            return null;
        } catch (PHPMailerException $ex) {
            return "falha ao enviar (" . $mail->ErrorInfo . ").";
        }
    }
}
