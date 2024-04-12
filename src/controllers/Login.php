<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Client;

class Login
{
    use Controller;

    private array $data;

    public function __construct()
    {
        // initialize default values
        $this->data['defaultEmail'] = "";
        $this->data['defaultPassword'] = "";
    }

    private function validateUser(): bool
    {
        // default error
        $this->data['errors']['other'] = 'You have entered a wrong email or password';

        // fetch client record
        $client = Client::getByEmail($this->data['defaultEmail']);

        if (!$client) {
            return false;
        }

        // validate password
        if (!$client->verifyPassword($this->data['defaultPassword'])) {
            return false;
        }

        // no errors
        unset($this->data['errors']['other']);
        return true;
    }

    public function index(): void
    {
        if (isset($_POST['login_submit'])) {
            // TODO: sanitize values

            // update default form values
            $this->data['defaultEmail'] = trim($_POST['email'] ?? "");
            $this->data['defaultPassword'] = trim($_POST['password'] ?? "");

            // check if credentials are correct
            if ($this->validateUser()) {
                // store user email in session
                $_SESSION['user'] = $this->data['defaultEmail'];

                // regenerate session id for security purposes
                // Reference: https://stackoverflow.com/a/34206189/17627866
                session_regenerate_id();

                // redirect user to his profile
                Utility::redirect('profile');
            }
        }

        $this->view(
            'Login',
            $this->data,
            'Login',
            template_tags: $this->getLibrariesTags(['aos']),
            template_meta_description: "Sign in to Steamy Sips and unlock a world of aromatic delights. Access your account, manage orders, and enjoy a seamless shopping experience tailored just for you."
        );
    }
}
