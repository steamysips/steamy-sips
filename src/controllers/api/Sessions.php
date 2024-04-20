<?php

declare(strict_types=1);

namespace Steamy\Controller\API;

use Steamy\Model\Administrator;

/**
 * Handles /sessions route of API
 */
class Sessions
{
    private function handleLogin(): void
    {
        $email = trim($_POST['email'] ?? "");
        $password = trim($_POST['password'] ?? "");

        if (empty($email) || empty($password)) {
            http_response_code(400);
            die();
        }

        // fetch administrator account
        $admin = Administrator::getByEmail($email);

        // validate email
        if (!$admin) {
            http_response_code(401);
            die();
        }

        // validate password
        if (!$admin->verifyPassword($password)) {
            http_response_code(401);
            die();
        }

        $_SESSION['admin_email'] = $email;
        session_regenerate_id();
    }

    public function index(): void
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $this->handleLogin();
                break;
            default:
                http_response_code(400);
                die();
        }
    }
}