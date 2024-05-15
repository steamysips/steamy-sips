<?php

declare(strict_types=1);

namespace Steamy\Model;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Class for sending mails
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
     * @param string $email Email address of recipient
     * @param string $subject Email subject line
     * @param string $html_message Message body as an HTML string
     * @param string $plain_message Message as plain text
     * @return bool false on error - See the ErrorInfo property for details of the error
     * @throws Exception Error when calling addAddress or msgHTML
     */
    public function sendMail(string $email, string $subject, string $html_message, string $plain_message = ""): bool
    {
        //Set who the message is to be sent to
        $this->mail->addAddress($email);

        //Set the subject line
        $this->mail->Subject = $subject;

        // Read an HTML message body from an external file, convert referenced images to embedded,
        // convert HTML into a basic plain-text alternative body
        $this->mail->msgHTML($html_message);

        // Replace the plain text body with one created manually
        if (strlen($plain_message) > 0) {
            $this->mail->AltBody = $plain_message;
        }

        // Send the message
        return $this->mail->send();
    }

    /**
     * @throws Exception
     */
    public function sendOrderConfirmationEmail(Order $order): bool
    {
        $client = Client::getByID($order->getClientID());
        if (empty($client)) {
            return false;
        }

        $store = $order->getStore();
        if (empty($store)) {
            return false;
        }

        // fill email template and save to a variable
        ob_start();
        require_once __DIR__ . '/../views/mails/OrderConfirmation.php';
        $html_message = ob_get_contents();
        ob_end_clean();

        return $this->sendMail(
            $client->getEmail(),
            "Order Confirmation | Steamy Sips",
            $html_message
        );
    }

}

