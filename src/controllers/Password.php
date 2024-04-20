<?php

namespace Steamy\Controller;

use Steamy\Core\Utility;
use Steamy\Model\User;
use Steamy\Core\Controller;

// Define PUBLIC_ROOT constant if not defined
if (!defined('PUBLIC_ROOT')) {
    define('PUBLIC_ROOT', 'http://localhost:8080/steamy-sips/public');
}

class Password
{
    use Controller; 

    public function index()
    {
        // If email submitted
        if (isset($_POST['usersEmail']) && filter_var($_POST['usersEmail'], FILTER_VALIDATE_EMAIL)) {
            $email = $_POST['usersEmail'];

            // Generate random token
            $token = bin2hex(random_bytes(16)); // Generating a random token of length 32 bytes (hexadecimal format)

            // Save information about request in the password_change_request table
            $expiryDate = date('Y-m-d H:i:s', strtotime('+1 day')); // Expiry date set to 1 day from now
            $tokenHash = password_hash($token, PASSWORD_DEFAULT); // Hashing the token for security
            $userId = User::getUserIdByEmail($email); // Get user ID by email

            if ($userId !== false) {
                User::savePasswordChangeRequest($userId, $tokenHash, $expiryDate);
                
                // Send email with reset link
                $resetLink = PUBLIC_ROOT . "/password/reset?id=$token";
                User::sendResetEmail($email, $resetLink);

            } else {
                Utility::redirect('login');
            }
         } else {
            // Display forgot password form
            require_once PUBLIC_ROOT . 'resetpassword.php';
        }

        $this->view(
            'ResetPassword',
            ['email' => $email], // Pass email data to the view
            'ResetPassword'
        );

        if (isset($_POST['pwd'], $_POST['pwd-repeat'], $_GET['id'])) {
            $password = $_POST['pwd'];
            $passwordRepeat = $_POST['pwd-repeat'];
            $token = $_GET['id'];

            // Check if passwords match
            if ($password === $passwordRepeat) {
                // Hash the new password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Get user ID based on token
                $userId = User::getUserIdByToken($token);

                if ($userId !== false) {
                    // Update user's password
                    User::updatePassword($userId, $hashedPassword);

                    // Redirect to login page
                    Utility::redirect('login');
                }
            } else {        
                // If passwords don't match or token is invalid, redirect to reset password page
                Utility::redirect('resetpassword');	
            }
        }

        $this->view(
            'Newpassword',
            ['pwd' => '', 'pwd-repeat' => ''], 
            'Newpassword'
        );

    }
}
