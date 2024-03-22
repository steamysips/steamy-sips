<?php

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Client;

class Login
{
    use Controller;

    private array $data;

    private function validateUser(): bool
    {
        // default error
        $this->data['errors']['other'] = 'You have entered a wrong email or password';

        $client = Client::getByEmail($this->data['defaultEmail']);
        Utility::show($client);
        if (empty($client)) {
            Utility::show("account does not exist");
            return false;
        }

        // validate password
        if (!password_verify($this->data['defaultPassword'], $client->getPassword())) {
            Utility::show("invalid password");
            Utility::show($this->data['defaultPassword']);
            Utility::show($client->getPassword());

            return false;
        }

        // no errors
        unset($this->data['errors']['other']);
        return true;
    }

    public function index(): void
    {
        // initialize default values
        $this->data['defaultEmail'] = "";
        $this->data['defaultPassword'] = "";

        if (isset($_POST['login_submit'])) {
            // TODO: sanitize values

            // update default form values
            $this->data['defaultEmail'] = $_POST['email'] ?? "";
            $this->data['defaultPassword'] = $_POST['password'] ?? "";

            // check if credentials are correct
            if ($this->validateUser()) {
                // setup session and redirect to dashboard
                $_SESSION['user'] = $this->data['defaultEmail'];
                Utility::redirect('profile');
            }
        }

        $this->view(
            'Login',
            $this->data,
            'Login'
        );
    }
}
