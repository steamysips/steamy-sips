<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Exception;
use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Mailer;
use Steamy\Model\User;

/**
 * Controller responsible for managing the entire password reset user flow. It is invoked
 * for root-relative urls of the form `/password`. The following actions are performed:
 * 1. Display a form asking for user email.
 * 2. Handle email submission.
 * 3. Send email to user.
 * 4. Display form asking for new password
 * 4. Handle new password submission.
 *
 * The user flow for resetting passwords was adapted from https://stackoverflow.com/a/1102817/17627866
 */
class Password
{
    use Controller;

    private array $view_data = [];

    public function __construct()
    {
        $this->view_data['email_submit_success'] = false;
        $this->view_data['error'] = false;
        $this->view_data['password_change_success'] = false;
    }

    /**
     * Sends an email with a password reset link
     * @throws Exception
     */
    private function sendResetEmail(string $email, string $resetLink): void
    {
        $subject = "Reset Your Password | Steamy Sips";

        // Capture the HTML template content
        ob_start();
        $userEmail = $email;
        require __DIR__ . '/../views/mails/PasswordReset.php';
        $htmlMessage = ob_get_clean();

        // Plain message as fallback
        $plainMessage = "Click the link below to reset your password:\n$resetLink";

        // Send the email
        $mailer = new Mailer();
        $mailer->sendMail($email, $subject, $htmlMessage, $plainMessage);
    }

    /**
     * Invoked when  user submits an email on form.
     * @throws Exception Email could not be sent
     */
    private function handleEmailSubmission(): void
    {
        $submitted_email = filter_var($_POST['email'] ?? "", FILTER_VALIDATE_EMAIL);

        // check if email has a valid format
        if (empty($submitted_email)) {
            $this->view_data['error'] = 'Invalid email';
            return;
        }

        // get user ID corresponding to user email
        $userId = User::getUserIdByEmail($submitted_email);

        // check if account is not present in database
        if (empty($userId)) {
            $this->view_data['error'] = 'Email does not exist';
            return;
        }

        // Generate a token for a password change request
        try {
            $token_info = User::generatePasswordResetToken($userId);
        } catch (Exception) {
            $this->view_data['error'] = 'Mailing service is not operational. Try again later';
            return;
        }

        // Send email to user with password reset link and user id
        $passwordResetLink = Utility::getRoot() . "/password/reset?token=" . $token_info['token'] .
            "&id=" . $token_info['request_id'];

        $this->sendResetEmail($submitted_email, $passwordResetLink);
        $this->view_data['email_submit_success'] = true;
    }

    /**
     * Checks if password reset link contains the necessary token and id query parameters.
     * @return bool True if valid
     */
    private function validatePasswordResetLink(): bool
    {
        // check if query parameters are present
        if (empty($_GET['token']) || empty($_GET['id'])) {
            return false;
        }

        // validate request id data type
        if (!filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
            return false;
        }

        return true;
    }

    /**
     * This function is invoked when user opens password reset link from email
     * and submits form.
     * @return void
     */
    private function handlePasswordSubmission(): void
    {
        if (!$this->validatePasswordResetLink()) {
            $this->view_data['error'] = 'Invalid password reset link';
            return;
        }

        if (!isset($_POST['pwd'], $_POST['pwd-repeat'])) {
            $this->view_data['error'] = 'You must enter new password twice';
            return;
        }

        $password = $_POST['pwd'];
        $passwordRepeat = $_POST['pwd-repeat'];
        $token = $_GET['token'];
        $requestID = filter_var($_GET['id'], FILTER_VALIDATE_INT);

        // Check if passwords match
        if ($password !== $passwordRepeat) {
            $this->view_data['error'] = 'Passwords do not match';
            return;
        }

        // check if password valid
        $password_errors = User::validatePlainPassword($password);
        if (!empty($password_errors)) {
            $this->view_data['error'] = $password_errors[0];
            return;
        }

        $success = User::resetPassword($requestID, $token, $password);

        if ($success) {
            $this->view_data['password_change_success'] = true;
        } else {
            $this->view_data['error'] = 'Failed to change password. Try generating a new token.';
        }
    }

    public function index(): void
    {
        // check if url is of form /password
        if (Utility::getURL() === 'password') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // user has submitted his email
                try {
                    $this->handleEmailSubmission();
                } catch (Exception) {
                    $this->view_data['error'] = 'Mailing service is not operational. Please try again later.';
                }
            }
            // display form asking for user email
            // this form should be displayed before and after email submission
            $this->view(
                view_name: 'ResetPassword',
                view_data: $this->view_data,
                template_title: 'Reset Password',
                enableIndexing: false
            );
            return;
        }

        // check if url is of form /password/reset
        if (Utility::getURL() === 'password/reset') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $this->handlePasswordSubmission();
            }
            // display form asking user for his new password
            $this->view(
                view_name: 'NewPassword',
                view_data: $this->view_data,
                template_title: 'New Password',
                enableIndexing: false
            );
            return;
        }

        // if url follows some other format display error page
        (new Error())->handlePageNotFoundError();
    }
}

