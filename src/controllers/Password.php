<?php

declare(strict_types=1);

namespace Steamy\Controller;

use PHPMailer\PHPMailer\Exception;
use Steamy\Core\Mailer;
use Steamy\Model\User;
use Steamy\Core\Controller;

/**
 * Displays form asking for email and handles email submission for password reset.
 */
class Password
{
    use Controller;

    /**
     * @throws Exception
     */
    private function sendResetEmail(string $email, string $resetLink): void
    {
        //Implement logic to send reset email using Mailer class
        $mailer = new Mailer();
        $subject = "Reset Your Password";
        $htmlMessage = "Click the link below to reset your password:<br><a href='$resetLink'>$resetLink</a>";
        $plainMessage = "Click the link below to reset your password:\n$resetLink";
        $mailer->sendMail($email, $subject, $htmlMessage, $plainMessage);
    }

    private function handleEmailSubmission(): void
    {
        $submitted_email = filter_var($_POST['email'] ?? "", FILTER_VALIDATE_EMAIL);

        if (empty($submitted_email)) {
            return;
        }
        // email is valid

        // Generate random token
        $token = bin2hex(random_bytes(16)); // Generating a random token of length 32 bytes (hexadecimal format)

        // Save information about request in the password_change_request table
        $expiryDate = date('Y-m-d H:i:s', strtotime('+1 day')); // Expiry date set to 1 day from now
        $tokenHash = password_hash($token, PASSWORD_BCRYPT); // Hashing the token for security
        $userId = User::getUserIdByEmail($submitted_email); // Get user ID by email

        if ($userId) {
            User::savePasswordChangeRequest($userId, $tokenHash, $expiryDate);
            $resetLink = ROOT . "/password/reset?id=$token";

            try {
                $this->sendResetEmail($submitted_email, $resetLink);
            } catch (Exception $e) {
                echo 'Mailer credentials invalid';
            }
        } else {
            echo $submitted_email . " not in database";
        }
    }

    public function index(): void
    {
        $this->handleEmailSubmission();

        // display form asking for user email
        {
            $this->view(
                view_name: 'ResetPassword',
                template_title: 'Reset Password'
            );
        }
    }
}
