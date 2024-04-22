<?php

declare(strict_types=1);

namespace Steamy\Core;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Steamy\Model\Client;

/**
 * Class for sending mails to clients
 *
 * Reference: https://github.com/PHPMailer/PHPMailer/blob/master/examples/gmail.phps
 */
class Mailer
{
    private PHPMailer $mail;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        //Create a new PHPMailer instance
        $this->mail = new PHPMailer(true); // class will throw exceptions on errors, which we need to catch

        //Tell PHPMailer to use SMTP
        $this->mail->isSMTP();

        //Enable SMTP debugging
        //SMTP::DEBUG_OFF = off (for production use)
        //SMTP::DEBUG_CLIENT = client messages
        //SMTP::DEBUG_SERVER = client and server messages
        $this->mail->SMTPDebug = SMTP::DEBUG_OFF;

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
        $this->mail->Username = $_ENV['BUSINESS_GMAIL'];

        //Password to use for SMTP authentication
        $this->mail->Password = $_ENV['BUSINESS_GMAIL_PASSWORD'];

        //Set who the message is to be sent from
        //Note that with gmail you can only use your account address (same as `Username`)
        $this->mail->setFrom($_ENV['BUSINESS_GMAIL'], 'Steamy Sips');
    }

    /**
     * @throws Exception
     */
    public function sendMail(string $email, string $subject, $html_message, $plain_message): void
    {
        //Set who the message is to be sent to
        $this->mail->addAddress($email);

        //Set the subject line
        $this->mail->Subject = $subject;

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $this->mail->msgHTML($html_message);

        //Replace the plain text body with one created manually
        $this->mail->AltBody = $plain_message;

        //send the message
        $this->mail->send();
    }
}

