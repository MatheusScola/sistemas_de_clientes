<?php
// Definindo Plugin.
use PHPMailer\PHPMailer\PHPMailer;

// Função responsável por eviar os e-mails
function send_email($Address, $subJect, $bodyHTML ) {

    // Carregando os plugins utilizados no sistema.
    require 'vendor/autoload.php';

    // Criando novo objeto.
    $mail = new PHPMailer;

    // Definindo qual protocolo vai ser utilizado.
    $mail->isSMTP();

    // Configurando o Debug.
    $mail->SMTPDebug = 0;

    // Passando o Host desejado.
    $mail->Host = 'smtp.gmail.com';

    // Definindo a porta de acesso.
    $mail->Port=465;

    // Ativando conferência para acesssar o servidor smtp.
    $mail->SMTPAuth = true;

    // Definindo o nome do usuário.
    $mail->Username = 'matheus.fernan01@gmail.com';

    // Definindo a senha.
    $mail->Password = 'ozimhxywppvbtizs';

    // Definindo a segurança do E-mail.
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

    // Informando que o texto do E-mail vai ser composto por tags HTML.
    $mail->isHTML(true);

    // Definindo a codificação do E-mail.
    $mail->CharSet = 'UTF-8';

    // Definindo o remetente do E-mail.
    $mail->setFrom('matheus.fernan01@gmail.com.br', 'Matheus Fernandes');

    // Definindo destinatário.
    $mail->addAddress($Address);

    // Assunto do E-mail.
    $mail->Subject = $subJect;

    // Texto do E-mail.
    $mail->Body = $bodyHTML;

    // Enviando o E-mail
    if($mail->send()) {
        return True;

    } else {
        Return "Falha ao enviar e-mail: " . $mail->ErrorInfo;
    }
}
?>