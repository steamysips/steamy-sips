<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Client;

class Login
{
    use Controller;

    private array $view_data;

    public function __construct()
    {
        // initialize default form value
        $this->view_data['defaultEmail'] = "";
    }

    /**
     * Checks if user record exists in database
     * @param string $email
     * @param string $password
     * @return bool True if it exists, false otherwise
     */
    private function validateUser(string $email, string $password): bool
    {
        // fetch client record
        $client = Client::getByEmail($email);

        if (!$client) {
            return false;
        }

        // validate password
        if (!$client->verifyPassword($password)) {
            return false;
        }

        // no errors
        return true;
    }

    private function handleFormSubmission(): void
    {
        // get un-sanitized version of email which may contain special characters
        // Ref: https://blog.mutantmail.com/can-email-addresses-have-special-characters/
        $entered_email = htmlspecialchars_decode(trim($_POST['email'] ?? ""));

        // leave password unchanged as leading/trailing spaces can be part of password
        // Ref: https://stackoverflow.com/a/7240898/17627866
        $entered_password = $_POST['password'] ?? "";


        // check if credentials are correct
        if ($this->validateUser($entered_email, $entered_password)) {
            // store user email in session
            $_SESSION['user'] = $entered_email;

            // regenerate session id for security purposes
            // Ref: https://stackoverflow.com/a/34206189/17627866
            session_regenerate_id();

            // redirect user to his profile
            Utility::redirect('profile');
        } else {
            // user entered invalid credentials

            // update default form value to sanitized version of entered email
            $this->view_data['defaultEmail'] = htmlspecialchars($entered_email);
        }
    }

    public function index(): void
    {
        if (isset($_POST['login_submit'])) {
            $this->handleFormSubmission();
        }

        $this->view(
            'Login',
            $this->view_data,
            'Login',
            template_tags: $this->getLibrariesTags(['aos']),
            template_meta_description: "Sign in to Steamy Sips and unlock a world of aromatic delights. Access your account, manage orders, and enjoy a seamless shopping experience tailored just for you."
        );
    }
}
