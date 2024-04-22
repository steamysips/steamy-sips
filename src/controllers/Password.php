<?php

declare(strict_types=1);

namespace Steamy\Controller;

use PHPMailer\PHPMailer\Exception;
use Random\RandomException;
use Steamy\Core\Mailer;
use Steamy\Model\User;
use Steamy\Core\Controller;
use Steamy\Core\Utility;

/**
 * Controller responsible for managing entire password reset user flow. It
 * displays a form asking for user email, handles email submission, sends email,
 * handles submission for new password.
 */
class Password
{
    use Controller;

    private array $view_data = [];
    private bool $server_error;

    public function __construct()
    {
        $this->server_error = false;
        $this->view_data['email_submit_success'] = false;
    }

    /**
     * Sends an email with a password reset link
     * @throws Exception
     */
    private function sendResetEmail(string $email, string $resetLink): void
    {
        //Implement logic to send reset email using Mailer class
        $mailer = new Mailer();
        $subject = "Reset Your Password | Steamy Sips";
        $htmlMessage = "Click the link below to reset your password:<br><a href='$resetLink'>$resetLink</a>";
        $plainMessage = "Click the link below to reset your password:\n$resetLink";
        $mailer->sendMail($email, $subject, $htmlMessage, $plainMessage);
    }

    /**
     * @throws RandomException Token could not be generated
     * @throws Exception Email could not be sent
     */
    private function handleEmailSubmission(): void
    {
        $submitted_email = filter_var($_POST['email'] ?? "", FILTER_VALIDATE_EMAIL);

        if (empty($submitted_email)) {
            return;
        }
        // email is valid

        // get user ID corresponding to user email
        $userId = User::getUserIdByEmail($submitted_email); // Get user ID by email

        // if user is not present in database, simply return
        // Note: For privacy reasons, we do not inform the client as the person requesting
        // the password reset may not be the true owner of the email
        if (empty($userId)) {
            return;
        }

        // Get a token corresponding a password change request
        $tokenHash = User::savePasswordChangeRequest($userId);

        // Send email to user with password reset link
        $passwordResetLink = ROOT . "/password?token=$tokenHash";
        $this->sendResetEmail($submitted_email, $passwordResetLink);
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
                try {
                    $this->handleEmailSubmission();
                    $this->view_data['email_submit_success'] = true;
                } catch (\Exception $e) {
                    $this->server_error = true;
                }
            }

            if ($this->server_error) {
                // TODO: Call error handler
                echo 'Mailing service is down. Please try again later.';
            } else {
                // display form asking for user email
                // this form should be displayed before and after email submission
                $this->view(
                    view_name: 'ResetPassword',
                    view_data: $this->view_data,
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

