<?php

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\User;

class Dashboard
{
    use Controller;

    public function index(): void
    {
        // if user is unauthenticated, redirect to login page
        session_regenerate_id();
        if (!array_key_exists('user', $_SESSION) || !isset($_SESSION['user'])) {
            Utility::redirect('login');
        }

        $css_file = ROOT . "/styles/views/Dashboard.css";
        $data['users'] = (new User())->all();

        $this->view(
            'Dashboard',
            $data,
            'Dashboard',
        );
    }
}
