<?php

namespace PHPMailer\PHPMailer;

defined("BASEPATH" or die("El acceso al script no está permitido"));

define('EMAILCONFIG', 'email.ini');
define('MESSAGESENT', 'El mensaje ha sido enviado correctamente');
define('SERVER', 'mail.ingkar.es');
define('EMAIL', 'expedientes@ingkar.es');
define('MAILPSW', 's_iB@zX=enOq');
define('PORT', '995/pop3/ssl');

require_once APPPATH . 'libraries/class.phpmailer.php';
require_once APPPATH . 'libraries/class.smtp.php';

function sendmail($mailto, $subject, $body, $fromname = 'Asignación de trabajos', $toname = 'Encargado')
{

    // Usamos el mail de PHP, por el problema con el certificado de cloudges
    return mail($mailto, $subject, $body) ?
        MESSAGESENT :
        'Se ha producido un error al enviar el mensaje';

    if (file_exists(EMAILCONFIG)) {
        $file = parse_ini_file('email.ini');
    } else {
        redirect('mail/config');
    }

    $server = $file['server']; // "mail.rsanjoseo.com"
    $email = $file['email']; //"pruebas@rsanjoseo.com"
    $password = $file['password']; // ".09{wNB{lx0d"
    $port = $file['port']; // "110/pop3/notls"
    $outserver = $file['outserver']; // "mail.rsanjoseo.com"
    $outport = $file['outport']; //"25"

    $mail = new PHPMailer();
    //$mail->AddAttachment(“imagenes/imagenadjuntaalcorreo.jpg”, “nombre_escogido_a_mostrar.jpg”)

    //echo "<p>Conectando a $outserver por el puerto $outport. Con '$email' y '$password'.</p>";

    $mail->IsSMTP();

    $mail->From = $email;
    $mail->FromName = $fromname;
    $mail->Subject = utf8_decode($subject);
    //$mail->AltBody = $body;

    $mail->MsgHTML(nl2br($body));
    $mail->AddAddress($mailto, utf8_decode($toname));

    //$mail->SMTPDebug = 4;

    $mail->Host = $outserver;
    $mail->Port = $outport;
    $mail->SMTPSecure = $outport == 465 ? 'ssl' : 'tsl';
    $mail->SMTPAuth = true;

    // Esto es necesario en el servidor de WorkFrame, pero es mejor eliminarlo si hay SSL válido.
    /* Da igual, porque tampoco funciona. Da error de certificado.
    $mail->smtpConnect(
        array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true
            )
        )
    );
    */
    // Aquí finaliza el cambio que es mejor no poner si hay certificado SSL.

    $mail->Username = $email;
    $mail->Password = $password;

    if ($mail->Send()) {
        return MESSAGESENT;
    } else {
        return $mail->ErrorInfo;
    }
}
