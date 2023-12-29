<?php

class Login
{
    use Controller;

    private array $data;

    function validateUser($user): bool
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

    function index()
    {
        $user = new User;
        $css_file = ROOT . "/styles/views/Login.css";

        $this->data['defaultName'] = $user->getName();
        $this->data['defaultPassword'] = $user->getPassword();

        if (isset($_POST['login_submit'])) {
            $this->data['defaultName'] = $_POST['name'];
            $this->data['defaultPassword'] = $_POST['password'];

            $user->setName($_POST['name']);
            $user->setPassword($_POST['password']);

            unset($_POST['login_submit']);

            if ($this->validateUser($user)) {
                redirect('dashboard');
            }
        }

        $this->view('Login', $this->data, 'Login',
            "<link rel=\"stylesheet\" href=\"$css_file\"/>");
    }
}
