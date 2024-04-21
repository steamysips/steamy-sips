<?php

declare(strict_types=1);

namespace Steamy\Controller;

use PHPMailer\PHPMailer\Exception;
use Steamy\Core\Mailer;
use Steamy\Model\User;
use Steamy\Core\Controller;
use Steamy\Core\Utility;

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
            $resetLink = ROOT . "/password?token=$tokenHash";

            try {
                $this->sendResetEmail($submitted_email, $resetLink);
                echo 'Please check your email. We have sent you an email with a link to change your password';
            } catch (Exception $e) {
                echo 'Mailer credentials invalid';
            }
        } else {
            echo $submitted_email . " not in database";
        }
    }

    public function handlePasswordSubmission(): void
    {
        if (isset($_POST['pwd'], $_POST['pwd-repeat'], $_GET['token'])) {
            $password = $_POST['pwd'];
            $passwordRepeat = $_POST['pwd-repeat'];
            $token = $_GET['token'];

            // Check if passwords match
            if ($password === $passwordRepeat) {
                // Hash the new password
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Get user ID based on token
                $userId = User::getUserIdByToken($token);

                if ($userId !== null) {
                    // Update user's password
                    User::updatePassword($userId, $hashedPassword);

                    // Redirect to login page or display success message
                    Utility::redirect('login');
                } else {
                    // Handle invalid token (redirect to an error page or display an error message)
                    echo "Invalid token.";
                }
            } else {
                // Handle password mismatch error
                echo "Passwords do not match.";
            }
        } else {
            // Handle missing form data error
            echo "Form data is missing.";
        }
    }

    public function index(): void
    {
        if (empty($_GET['token'])) {
            // user is accessing /password for the first time

            if (!empty($_POST['email'])) {
                // user has submitted his email
                $this->handleEmailSubmission();
            } else {
                // display form asking for user email
                $this->view(
                    view_name: 'ResetPassword',
                    template_title: 'Reset Password'
                );
            }
        } elseif (!empty($_POST['pwd'])) {
            // user has submitted his new password
            $this->handlePasswordSubmission();
        } else {
            // ask user for his new password
            $this->view(
                view_name: 'Newpassword',
                template_title: 'New Password'
            );
        }
    }
}

