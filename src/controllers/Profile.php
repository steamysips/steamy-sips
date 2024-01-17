<?php

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Model\User;
class Profile
{
    use Controller;

    public function index(): void
    {
        // if user is not signed in, redirect to login page
        session_regenerate_id();
        if (!array_key_exists('user', $_SESSION) || !isset($_SESSION['user'])) {
            redirect('login');
        }

        // fetch user details from database
        $current_user = new User();

        // display user profile
        $this->view('Profile');
    }
}
