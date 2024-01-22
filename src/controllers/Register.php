<?php

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\User;

class Register
{
    use Controller;

    public function index(): void
    {
        $user = new User();
        $data['defaultName'] = ''; // initial value for name before submit
        $data['defaultPassword'] = ''; // initial value for password before submit
        $data['defaultConfirmPassword'] = ''; // initial  value for confirm password

        if (isset($_POST['register_submit'])) {
            $data['defaultName'] = $_POST['name'];
            $data['defaultPassword'] = $_POST['password'];
            $data['defaultConfirmPassword'] = $_POST['confirmPassword'];

            if ($user->validate($_POST)) {
                // remove extraneous $post values before insertion to database
                unset($_POST['register_submit']);
                unset($_POST['confirmPassword']);
                $user->insert($_POST);
                Utility::redirect('login');
            }
            $data['errors'] = $user->errors;
        }

        $this->view(
            'Register',
            $data,
            'Login'
        );
    }
}
