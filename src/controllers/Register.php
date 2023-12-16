<?php

class Register
{
    use Controller;
    public function index()
    {
        $user = new User;

        if ($user->validate($_POST)) {
            $user->insert($_POST);
            redirect('home');
        }
        $data['errors'] = $user->errors;

        $css_file = ROOT . "/styles/views/Register.css";

        $this->view('Register', $data, 'Login', "<link rel=\"stylesheet\" href=\"$css_file\"></link>");
    }
}
