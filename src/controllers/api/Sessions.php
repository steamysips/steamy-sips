<?php

declare(strict_types=1);

namespace Steamy\Controller\API;

use Steamy\Model\Administrator;

/**
 * Handles /sessions route of API
 */
class Sessions
{
    public static array $routes = [
        'POST' => [
            '/sessions' => 'handleLogin',
        ]
    ];

    /**
     * Create a new session for an administrator if credentials are valid.
     * @return void
     */
    public function handleLogin(): void
    {
        $data = (object)json_decode(file_get_contents("php://input"), true);
        $email = trim($data->email ?? "");
        $password = trim($data->password ?? "");

        if (empty($email) || empty($password)) {
            http_response_code(400);
            die();
        }

        // fetch administrator account
        $admin = Administrator::getByEmail($email);

        // validate credentials
        if (!$admin || !$admin->verifyPassword($password)) {
            http_response_code(401);
            die();
        }

        $_SESSION['admin_email'] = $email;
        session_regenerate_id();
        http_response_code(201);
    }
}