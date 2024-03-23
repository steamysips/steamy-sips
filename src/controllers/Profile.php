<?php

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Client;

class Profile
{
    use Controller;

    private Client $client;
    private array $data;

    public function __construct()
    {
        // filter out unsigned users
        $this->handleUnsignedUsers();

        // fetch user details from database
        $this->client = Client::getByEmail($_SESSION['user']);
    }

    private function handleLogOut(): void
    {
        $_SESSION = array();
        session_destroy();

        // regenerate session id for security purposes
        // Reference: https://stackoverflow.com/a/34206189/17627866
        session_regenerate_id();

        // redirect user to login page
        Utility::redirect('login');
    }

    private function handleAccountDeletion(): void
    {
        // delete user account if delete button clicked
        $this->client->deleteUser();
        $this->handleLogOut();
    }

    /**
     * Check if current user is signed in and if not redirects
     * him to login page
     *
     * @return void
     */
    private function handleUnsignedUsers(): void
    {
        if (!array_key_exists('user', $_SESSION) || !isset($_SESSION['user'])) {
            Utility::redirect('login');
        }
    }

    public function index(): void
    {
        // log out user if logout button clicked
        if (isset($_POST['logout_submit'])) {
            $this->handleLogOut();
        }

        // delete user account if delete button clicked
        if (isset($_POST['account_delete_submit'])) {
            $this->handleAccountDeletion();
        }

        // TODO: fetch 5 latest orders
        $this->data["orders"] = array_fill(
            0,
            5,
            (object)[
                'date' => '16/01/2024',
                'id' => 4343,
                'cost' => 100.00,
                'status' => 'Completed'
            ]
        );

        // initialize user details for template
        $this->data["name"] = $this->client->getFullName();
        $this->data["email"] = $this->client->getEmail();
        $this->data["address"] = $this->client->getAddress();

        $this->view(
            'Profile',
            $this->data,
            'Profile'
        );
    }
}
