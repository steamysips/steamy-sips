<?php

declare(strict_types=1);

namespace Steamy\Controller;

use DateTime;
use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Client;
use Steamy\Model\District;
use Steamy\Model\Location;
use Steamy\Model\Order;
use Steamy\Model\OrderStatus;

class Profile
{
    use Controller;

    private ?Client $signed_client; // currently signed in client
    private array $view_data = [];

    public function __construct()
    {
        $this->signed_client = null;
        $this->view_data['errors'] = [];
        $this->view_data['client'] = null;
        $this->view_data['show_account_deletion_confirmation'] = false;
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
        $this->view_data['show_account_deletion_confirmation'] = true;

        // Check if the deletion confirmation has been submitted
        if (isset($_POST['confirm_delete'])) {
            // Perform account deletion
            $this->signed_client->deleteUser();
            $this->handleLogOut();
            return;
        }

        // Check if cancel button is clicked
        if (isset($_POST['cancel_delete'])) {
            Utility::redirect('profile');
            return;
        }

        // Render the view with the confirmation message
        $this->view(
            'Profile',
            $this->view_data,
            'Profile',
            enableIndexing: false
        );
    }

    /**
     * Checks if there is session data stored about current user. If so, this means that client is signed in.
     * If not signed in, redirect user to login page.
     *
     * @return void
     */
    private function handleUnsignedUsers(): void
    {
        if (empty($this->getSignedInClient())) {
            Utility::redirect('login');
        }
    }

    private function displayProfileDetails(Client $client, string $password = "", string $password_confirm = ""): void
    {
        $this->view_data['defaultFirstName'] = $client->getFirstName();
        $this->view_data['defaultLastName'] = $client->getLastName();
        $this->view_data['defaultPhoneNumber'] = $client->getPhoneNo();
        $this->view_data['defaultStreet'] = $client->getAddress()->getStreet();
        $this->view_data['defaultCity'] = $client->getAddress()->getCity();
        $this->view_data['defaultDistrictID'] = $client->getAddress()->getDistrictID();
        $this->view_data['defaultEmail'] = $client->getEmail();
        $this->view_data['districts'] = District::getAll();
        $this->view_data['defaultPassword'] = $password;
        $this->view_data['defaultConfirmPassword'] = $password_confirm;

        $this->view(
            'Register',
            $this->view_data,
            'Edit profile',
            enableIndexing: false
        );
    }

    public function reorderOrder(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['order_id'])) {
            // Handle invalid request
            Utility::redirect('profile');
        }

        $order_id = (int)$_POST['order_id'];
        $order = Order::getByID($order_id);

        if (!$order || $order->getStatus() !== OrderStatus::COMPLETED) {
            // Order doesn't exist or not completed
            Utility::redirect('profile');
        }

        // Create a new order with the same details as the previous order
        $new_order = new Order(
            store_id: $order->getStoreID(),
            client_id: $order->getClientID(),
            line_items: $order->getLineItems(),
            pickup_date: null, // or set pickup date as needed
            status: OrderStatus::PENDING,
            created_date: new DateTime()
        );

        // Save the new order
        $new_order->save();

        // Redirect back to the profile page
        Utility::redirect('profile');
        }


    public function cancelOrder(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['order_id'])) {
            // Handle invalid request
            Utility::redirect('profile');
        }

        $order_id = (int)$_POST['order_id'];
        $order = Order::getByID($order_id);

        if (!$order || $order->getStatus() === OrderStatus::COMPLETED) {
            // Order doesn't exist or already completed
            Utility::redirect('profile');
        }

        // Cancel the order
        $order->deleteOrder();

        // Redirect back to the profile page
        Utility::redirect('profile');
    }


    private function handleProfileEditSubmission(): void
    {
        $form_data = (new Register())->getFormData();

        // create a new client object
        $updated_client = new Client(
            email: $form_data['email'],
            first_name: $form_data['first_name'],
            last_name: $form_data['last_name'],
            plain_password: $form_data['password'],
            phone_no: $form_data['phone_no'],
            address: new Location(
                street: $form_data['street'],
                city: $form_data['city'],
                district_id: $form_data['district']
            )
        );
        $updated_client->setUserID($this->signed_client->getUserID());

        // validate all attributes (except password) and store errors
        $this->view_data['errors'] = $updated_client->validate();

        // check if user entered a new email
        if (!empty($form_data['email']) && $form_data['email'] !== $this->signed_client->getEmail()) {
            // check if a newly typed email already exists in database
            if (!empty(
            Client::getByEmail($updated_client->getEmail())
            )) {
                $this->view_data['errors']['email'] = "Email already in use";
            }
        }

        // check if user entered a new password
        $password_changed = false;
        if (!empty($form_data['password'])) {
            $password_changed = true;
            // validate plain text password
            $password_errors = Client::validatePlainPassword($form_data['password']);
            if (!empty($password_errors)) {
                $this->view_data['errors']['password'] = $password_errors[0];
            }

            // check if passwords do not match
            if ($form_data['confirm_password'] !== $form_data['password']) {
                $this->view_data['errors']['confirmPassword'] = 'Passwords do not match';
            }
        }


        // if all data valid, update user record and redirect to login page
        if (empty($this->view_data['errors'])) {
            $success = $updated_client->updateUser($password_changed);

            if ($success) {
                $this->signed_client = $updated_client;
                Utility::redirect('profile');
            }

            echo 'error';
        } else {
            $this->displayProfileDetails($updated_client, $form_data['password'], $form_data['confirm_password']);
        }
    }

    private function validateURL(): bool
    {
        return in_array(Utility::getURL(), ['profile', 'profile/edit'], true);
    }

    private function handleInvalidURL(): void
    {
        if (!$this->validateURL()) {
            (new Error())->handlePageNotFoundError();
            die();
        }
    }

    public function index(): void
    {
        $this->handleInvalidURL();

        // filter out unsigned users
        $this->handleUnsignedUsers();

        // at this point, we know that the current user was previously signed in

        // fetch his user details from database
        $this->signed_client = $this->getSignedInClient();
        if ($this->signed_client) {
            $this->view_data['client'] = $this->signed_client;
        } else {
            // if user record is missing from database, redirect to login page
            Utility::redirect('login');
        }


        // log out user if logout button clicked
        if (isset($_GET['logout_submit'])) {
            $this->handleLogOut();
            return;
        }

        // delete user account if delete button clicked
        if (isset($_GET['account_delete_submit'])) {
            $this->handleAccountDeletion();
            return;
        }

        // handle profile edit on /profile/edit page
        if (Utility::getURL() === 'profile/edit') {
            $this->view_data['editMode'] = true;
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handleProfileEditSubmission();
            } else {
                $this->displayProfileDetails($this->signed_client);
            }
            return;
        }

        // Fetch orders for the signed-in client
        $orders = Order::getOrdersByClientId($this->signed_client->getUserID());

        $this->view_data["orders"] = $orders;

        // initialize user details for template
        $this->view_data["name"] = $this->signed_client->getFirstName() . " " . $this->signed_client->getLastName();
        $this->view_data["email"] = $this->signed_client->getEmail();
        $this->view_data["address"] = $this->signed_client->getAddress()->getFormattedAddress();

        $this->view(
            'Profile',
            $this->view_data,
            'Profile',
            enableIndexing: false
        );
    }
}
