<?php

namespace Steamy\Controller;

use Steamy\Core\Mailer;
use Steamy\Core\Utility;
use Steamy\Model\ResetPassword;
use Steamy\Model\User;

class ResetPasswords {
    private $resetModel;
    private $userModel;
    private $mailer;

    public function __construct(Mailer $mailer, ResetPassword $resetModel, User $userModel) {
        $this->mailer = $mailer;
        $this->resetModel = $resetModel;
        $this->userModel = $userModel;
    }

    public function sendEmail() {
        //Sanitize POST data
        $_POST = array_map('htmlspecialchars', $_POST);
        $usersEmail = trim($_POST['usersEmail']);

        if (empty($usersEmail)) {
            $_SESSION['flash'] = "Please input email";
            $_SESSION['flash_class'] = 'form-message form-message-red';
            Utility::redirect("../reset-password.php"); 
        }

        if (!filter_var($usersEmail, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = "Invalid email";
            $_SESSION['flash_class'] = 'form-message form-message-red';
            Utility::redirect("../reset-password.php"); 
        }
        
        // Will be used to query the user from the database
        $selector = bin2hex(random_bytes(8));
        // Will be used for confirmation once the database entry has been matched
        $token = random_bytes(32);
        // URL will vary depending on where the website is being hosted from
        $url = 'http://localhost/login/create-new-password.php?selector='.$selector.'&validator='.bin2hex($token);
        // Expiration date will last for half an hour
        $expires = date("U") + 1800;
        
        if (!$this->resetModel->deleteEmail($usersEmail)) {
            die("There was an error");
        }
        
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        if (!$this->resetModel->insertToken($usersEmail, $selector, $hashedToken, $expires)) {
            die("There was an error");
        }
        
        // Can Send Email Now
        $subject = "Reset your password";
        $message = "<p>We received a password reset request.</p>";
        $message .= "<p>Here is your password reset link: </p>";
        $message .= "<a href='".$url."'>".$url."</a>";

        $this->mailer->sendMail($usersEmail, $subject, $message, $message);
        
        // Set flash message
        $_SESSION['flash'] = "Check your email";
        $_SESSION['flash_class'] = 'form-message form-message-green';
        Utility::redirect("../reset-password.php");
    }

    public function resetPassword() {
        // Sanitize POST data
        $_POST = array_map('htmlspecialchars', $_POST);
        $data = [
            'selector' => trim($_POST['selector']),
            'validator' => trim($_POST['validator']),
            'pwd' => trim($_POST['pwd']),
            'pwd-repeat' => trim($_POST['pwd-repeat'])
        ];
        $url = '../create-new-password.php?selector=' . $data['selector'] . '&validator=' . $data['validator'];
    
        if (empty($_POST['pwd']) || empty($_POST['pwd-repeat'])) {
            $_SESSION['flash'] = "Please fill out all fields";
            $_SESSION['flash_class'] = 'form-message form-message-red';
            Utility::redirect($url);
        } elseif ($data['pwd'] != $data['pwd-repeat']) {
            $_SESSION['flash'] = "Passwords do not match";
            $_SESSION['flash_class'] = 'form-message form-message-red';
            Utility::redirect($url);
        } elseif (strlen($data['pwd']) < 6) {
            $_SESSION['flash'] = "Invalid password";
            $_SESSION['flash_class'] = 'form-message form-message-red';
            Utility::redirect($url);
        }
    
        $currentDate = date("U");
        if (!$row = $this->resetModel->resetPassword($data['selector'], $currentDate)) {
            $_SESSION['flash'] = "Sorry. The link is no longer valid";
            $_SESSION['flash_class'] = 'form-message form-message-red';
            Utility::redirect($url);
        }
    
        $tokenBin = hex2bin($data['validator']);
        $tokenCheck = password_verify($tokenBin, $row->pwdResetToken);
        if (!$tokenCheck) {
            $_SESSION['flash'] = "You need to re-Submit your reset request";
            $_SESSION['flash_class'] = 'form-message form-message-red';
            Utility::redirect($url);
        }
    
        $tokenEmail = $row->pwdResetEmail;
        if (!$this->userModel->findUserByEmailOrUsername($tokenEmail, $tokenEmail)) {
            $_SESSION['flash'] = "There was an error";
            $_SESSION['flash_class'] = 'form-message form-message-red';
            Utility::redirect($url);
        }
    
        $newPwdHash = password_hash($data['pwd'], PASSWORD_DEFAULT);
        if (!$this->userModel->resetPassword($newPwdHash, $tokenEmail)) {
            $_SESSION['flash'] = "There was an error";
            $_SESSION['flash_class'] = 'form-message form-message-red';
            Utility::redirect($url);
        }
    
        if (!$this->resetModel->deleteEmail($tokenEmail)) {
            $_SESSION['flash'] = "There was an error";
            $_SESSION['flash_class'] = 'form-message form-message-red';
            Utility::redirect($url);
        }
    
        $_SESSION['flash'] = "Password Updated";
        $_SESSION['flash_class'] = 'form-message form-message-green';
        Utility::redirect($url);
    }
    
}

$mailer = new Mailer();

$init = new ResetPasswords($mailer, $resetModel, $userModel);

// Ensure that the session is started
session_start();

// Ensure that user is sending a post request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($_POST['type']) {
        case 'send':
            $init->sendEmail();
            break;
        case 'reset':
            $init->resetPassword();
            break;
        default:
            Utility::redirect("../login.php"); 
    }
} else {
    Utility::redirect("../login.php");
}
