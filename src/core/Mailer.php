<?php

namespace Steamy\Core;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{
    private PHPMailer $mail;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        //Create a new PHPMailer instance
        $this->mail = new PHPMailer();

        //Tell PHPMailer to use SMTP
        $this->mail->isSMTP();

        //Enable SMTP debugging
        //SMTP::DEBUG_OFF = off (for production use)
        //SMTP::DEBUG_CLIENT = client messages
        //SMTP::DEBUG_SERVER = client and server messages
        $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;

        //Set the hostname of the mail server
        $this->mail->Host = 'smtp.gmail.com';
        //Use `$mail->Host = gethostbyname('smtp.gmail.com');`
        //if your network does not support SMTP over IPv6,
        //though this may cause issues with TLS

        //Set the SMTP port number:
        // - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
        // - 587 for SMTP+STARTTLS
        $this->mail->Port = 465;

        //Set the encryption mechanism to use:
        // - SMTPS (implicit TLS on port 465) or
        // - STARTTLS (explicit TLS on port 587)
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        //Whether to use SMTP authentication
        $this->mail->SMTPAuth = true;

        //Username to use for SMTP authentication - use full email address for gmail
        $this->mail->Username = 'example@gmail.com';

        //Password to use for SMTP authentication
        $this->mail->Password = 'app-password';

        //Set who the message is to be sent from
        //Note that with gmail you can only use your account address (same as `Username`)
        //or predefined aliases that you have configured within your account.
        //Do not use user-submitted addresses in here
        $this->mail->setFrom('rhog1867@gmail.com', 'Steamy Sips');
    }

    /**
     * @throws Exception
     */
    public function sendMail(): void
    {
        //Set who the message is to be sent to
        $this->mail->addAddress('cosmiczorve@gmail.com', 'John Doe');

        //Set the subject line
        $this->mail->Subject = 'PHPMailer GMail SMTP test';

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $this->mail->msgHTML('helloe world');

        //Replace the plain text body with one created manually
        $this->mail->AltBody = 'This is a plain-text message body';

        //Attach an image file
//        $this->mail->addAttachment('images/phpmailer_mini.png');

        //send the message, check for errors
        if (!$this->mail->send()) {
            echo 'Mailer Error: ' . $this->mail->ErrorInfo;
        } else {
            echo 'Message sent!';
        }
    }
}

