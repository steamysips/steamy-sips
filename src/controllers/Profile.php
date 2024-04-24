<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Client;

class Profile
{
    use Controller;

    private ?Client $signed_client; // currently signed in client
    private array $data;

    public function __construct()
    {
        $this->signed_client = null;

        // filter out unsigned users
        $this->handleUnsignedUsers();

        // at this point, we know that current user was previously signed in

        // fetch his user details from database
        $client_record = Client::getByEmail($_SESSION['user']);
        if ($client_record) {
            $this->signed_client = $client_record;
        } else {
            // if user record is missing from database, redirect to login page
            Utility::redirect('login');
        }
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
        $this->signed_client->deleteUser();
        $this->handleLogOut();
    }

    /**
     * Checks if there is session data stored about current user. If so, this means that client is signed in.
     * If not signed in, redirect user to login page.
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
        $this->data["name"] = $this->signed_client->getFirstName() . " " . $this->signed_client->getLastName();
        $this->data["email"] = $this->signed_client->getEmail();
        $this->data["address"] = $this->signed_client->getAddress();

        $this->view(
            'Profile',
            $this->data,
            'Profile',
            template_meta_description: "Welcome to your personalized corner at Steamy Sips. Manage your orders, update your preferences, and track your coffee journey effortlessly. Your satisfaction is our priority."
        );
    }
}
