<?php

class Register
{
    use Controller;

    public function index()
    {
        $css_file = ROOT . "/styles/views/Register.css";

        $user = new User;
        $data['defaultName'] = ''; // initial value for name before submit
        $data['defaultPassword'] = ''; // initial value for password before submit

        if (isset($_POST['register_submit'])) {
            $data['defaultName'] = $_POST['name'];
            $data['defaultPassword'] = $_POST['password'];

            if ($user->validate($_POST)) {
                unset($_POST['register_submit']); // remove extraneous post value
                $user->insert($_POST);
                redirect('home');
            }
            $data['errors'] = $user->errors;
        }

        $this->view('Register', $data, 'Login', "<link rel=\"stylesheet\" href=\"$css_file\"></link>");
    }
}
