<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Client;
use Steamy\Model\District;

class Register
{
    use Controller;

    private array $view_data;

    public function __construct()
    {
        // initialize view data
        $this->view_data['defaultFirstName'] = "";
        $this->view_data['defaultLastName'] = "";
        $this->view_data['defaultPhoneNumber'] = "";
        $this->view_data['defaultDistrictID'] = 7;
        $this->view_data['defaultStreet'] = "";
        $this->view_data['defaultCity'] = "";
        $this->view_data['defaultEmail'] = "";
        $this->view_data['defaultPassword'] = "";
        $this->view_data['defaultConfirmPassword'] = "";
        $this->view_data['errors'] = [];
        $this->view_data['districts'] = District::getAll();
    }

    private function handleFormSubmission(): void
    {
        // set view data so that submitted values are displayed back to form

        // TODO: add more sanitization
        $this->view_data['defaultFirstName'] = trim($_POST['first_name'] ?? "");
        $this->view_data['defaultLastName'] = trim($_POST['last_name'] ?? "");
        $this->view_data['defaultPhoneNumber'] = trim($_POST['phone_no'] ?? "");
        $this->view_data['defaultDistrictID'] = (int)filter_var(trim($_POST['district']), FILTER_SANITIZE_NUMBER_INT);
        $this->view_data['defaultStreet'] = trim($_POST['street'] ?? "");
        $this->view_data['defaultCity'] = trim($_POST['city'] ?? "");
        $this->view_data['defaultEmail'] = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $this->view_data['defaultPassword'] = trim($_POST['password'] ?? "");
        $this->view_data['defaultConfirmPassword'] = trim($_POST['confirmPassword'] ?? "");

        // TODO: If district ID is invalid, handle
        // create a new client object
        $client = new Client(
            email: $this->view_data['defaultEmail'],
            first_name: $this->view_data['defaultFirstName'],
            last_name: $this->view_data['defaultLastName'],
            plain_password: $this->view_data['defaultPassword'],
            phone_no: $this->view_data['defaultPhoneNumber'],
            district: District::getByID($this->view_data['defaultDistrictID']),
            street: $this->view_data['defaultStreet'],
            city: $this->view_data['defaultCity']
        );

        // validate all attributes, except password
        $this->view_data['errors'] = $client->validate();

        // check if email already exists in database
        if (!empty(Client::getByEmail($client->getEmail()))) {
            $this->view_data['errors']['email'] = "Email already in use";
        }

        // validate plain text password
        $password_errors = Client::validatePlainPassword($this->view_data['defaultPassword']);
        if (!empty($password_errors)) {
            $this->view_data['errors']['password'] = $password_errors [0];
        }

        // check if passwords do not match
        if ($this->view_data['defaultConfirmPassword'] !== $this->view_data['defaultPassword']) {
            $this->view_data['errors']['confirmPassword'] = 'Passwords do not match';
        }

        // if all data valid, save new record and redirect to login page
        if (empty($this->view_data['errors'])) {
            $client->save();
            Utility::redirect('login');
        }
    }

    public function index(): void
    {
        if (isset($_POST['register_submit'])) {
            $this->handleFormSubmission();
        }

        $this->view(
            'Register',
            $this->view_data,
            'Register',
            template_meta_description: "Join the Steamy Sips community today. Register for exclusive offers, personalized recommendations, and a richer coffee experience. Start your journey towards flavorful indulgence."
        );
    }
}
