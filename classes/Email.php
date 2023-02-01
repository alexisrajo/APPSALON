<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;




class Email {

    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
        //CREAR EL OBJETO DE EMAIL
       
       
/*         // Instantiation and passing [ICODE]true[/ICODE] enables exceptions
        $mail = new PHPMailer(true);

        try {
        //Server settings
        $mail->SMTPDebug = 2; // Enable verbose debug output
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host = 'p3plzcpnl489493.prod.phx3.secureserver.net;mail.hotelpanamericanohn.com'; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'info@hotelpanamericanohn.com'; // SMTP username
        $mail->Password = 'info@hotel'; // SMTP password
        $mail->Port = 465; // TCP port to connect to

        //Recipients
        $mail->setFrom('info@hotelpanamericanohn.com', 'Mailer');
        $mail->addAddress('alexisrajo@gmail.com', 'gmail.com');
        $mail->addAddress('alexisrajo@me.com', 'me.com');
        $mail->addReplyTo('alexisrajo@hotmail.com', 'Information');
        $mail->addReplyTo('gerencia@hotelpanamericanohn.com', 'Information');
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Confirma tu Cuenta';

        $mail->Body = 'This is the HTML message body <b>in bold!</b>';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
        $mail->send();
        echo 'Message has been sent';
        
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        } */


        $mail = new PHPMailer();
        try {
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'd4c853280190b7';
        $mail->Password = '2cbb18448e3999';

        //CONFIGURAR CONTENIDO DEL EMAIL
        $mail->setFrom('alexisrajo@hotmail.com', 'Mailer');
        $mail->addAddress('alexisrajo@hotmail.com', 'hotmail.com');
        $mail->addReplyTo('alexisrajo@gmail.com', 'Information');
        $mail->Subject = 'Confirma tu Cuenta';

        // Set HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8'; 


        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has creado tu cuenta en App salon,
        solo debes confirmarla presionando el siguiente enlace</p>";
        $contenido .= "<p>Presiona aqui: <a href='http://localhost:4000/confirmar-cuenta?token=" . $this->token . "'>
        Confirmar Cuenta</a> </p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
        $contenido .= '</html>';
        $mail->Body = $contenido;

        //ENVIAR EL MAIL
        $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            exit;
        }
    }

    public function enviarInstrucciones() {
        $mail = new PHPMailer();
        try {
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'd4c853280190b7';
        $mail->Password = '2cbb18448e3999';

        //CONFIGURAR CONTENIDO DEL EMAIL
        $mail->setFrom('alexisrajo@hotmail.com', 'Mailer');
        $mail->addAddress('alexisrajo@hotmail.com', 'hotmail.com');
        $mail->addReplyTo('alexisrajo@gmail.com', 'Information');
        $mail->Subject = 'Reestablece tu contrasena';

        // Set HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8'; 


        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has solicitado reestablecer tu password, sigue
        el siguiente enlace para hacerlo.</p>";
        $contenido .= "<p>Presiona aqui: <a href='http://localhost:4000/recuperar?token=" . $this->token . "'>
        Reestablecer contrasena</a> </p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
        $contenido .= '</html>';
        $mail->Body = $contenido;

        //ENVIAR EL MAIL
        $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            exit;
        }
    }
}