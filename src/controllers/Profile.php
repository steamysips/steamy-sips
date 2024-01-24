<?php

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\User;

class Profile
{
    use Controller;

    public function index(): void
    {
        // if user is not signed in, redirect to login page
        session_regenerate_id();
        if (!array_key_exists('user', $_SESSION) || !isset($_SESSION['user'])) {
            Utility::redirect('login');
        }

        // log out user if logout button clicked
        if (isset($_POST['logout_submit'])) {
            $_SESSION = array();
            Utility::redirect('login');
        }

        // fetch user details from database
        $current_user = new User();

        // fetch 5 latest orders
        $data["orders"] = array_fill(
            0,
            5,
            (object)[
                'date' => '16/01/2024',
                'id' => 4343,
                'cost' => 100.00,
                'status' => 'Completed'
            ]
        );

        // display user profile
        $this->view(
            'Profile',
            $data,
            'Profile'
        );
    }
}
