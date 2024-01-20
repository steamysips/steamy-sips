<?php

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Model\User;

class Login
{
    use Controller;

    private array $data;

    private function validateUser($user): bool
    {
        if ($user->validate($_POST)) {
            $user_profile = $user->where($_POST);

            show($user_profile);

            if (empty($user_profile)) {
                $this->data['errors']['other'] = 'Account does not exist';
                return false;
            }

            return true;
        }
        $this->data['errors'] = $user->errors;
        return false;
    }

    public function index(): void
    {
        $user = new User();
        $css_file = ROOT . "/styles/views/Login.css";

        $this->data['defaultName'] = $user->getName();
        $this->data['defaultPassword'] = $user->getPassword();

        if (isset($_POST['login_submit'])) {
            // save values entered by user so that form
            // maintains its state if an error occurs
            $this->data['defaultName'] = $_POST['name'];
            $this->data['defaultPassword'] = $_POST['password'];

            $user->setName($_POST['name']);
            $user->setPassword($_POST['password']);

            unset($_POST['login_submit']);

            if ($this->validateUser($user)) {
                // user details are correct
                // setup session and redirect to dashboard
                $_SESSION['user'] = $user->getName();
                redirect('dashboard');
            }
        }

        $this->view(
            'Login',
            $this->data,
            'Login'
        );
    }
}
