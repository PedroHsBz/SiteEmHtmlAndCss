<?php
/* ==========================================================================
 *  config.local.exemplo.php  (modelo, pode ir para o GitHub)
 *
 *  1. Copie este arquivo e renomeie a cópia para  config.local.php
 *  2. Preencha com a conta do Gmail que vai enviar os e-mails.
 *
 *  O config.local.php fica no .gitignore, então a senha NÃO vai para o
 *  GitHub. Use uma SENHA DE APP do Google, não a senha normal do Gmail:
 *  Conta Google > Segurança > Verificação em duas etapas > Senhas de app.
 * ========================================================================== */

define("SMTP_USUARIO",   "sua.conta@gmail.com");
define("SMTP_SENHA_APP", "xxxx xxxx xxxx xxxx");   // 16 letras geradas pelo Google
