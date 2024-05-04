<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Client;
use Steamy\Model\District;
use Steamy\Model\Location;

/**
 * Controller for handling client account registration
 */
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
        $this->view_data['editMode'] = false;

        // get list of districts to be displayed on form
        $this->view_data['districts'] = District::getAll();
    }

    /**
     * Returns the un-sanitized version of the form data. Form data attributes are guaranteed to have the right
     * data types.
     * @return array An array indexed by attribute name. It contains all the required attributes.
     */
    public function getFormData(): array
    {
        $form_data = [];
        // $form_data will store all attributes and missing attributes are set to empty string

        $form_data['first_name'] = trim($_POST['first_name'] ?? "");
        $form_data['last_name'] = trim($_POST['last_name'] ?? "");
        $form_data['phone_no'] = trim($_POST['phone_no'] ?? "");

        // get district id as an integer. If districtID is missing, set it to -1
        $form_data['district'] = (int)filter_var(
            trim($_POST['district'] ?? "-1"),
            FILTER_SANITIZE_NUMBER_INT
        );

        $form_data['street'] = trim($_POST['street'] ?? "");
        $form_data['city'] = trim($_POST['city'] ?? "");
        $form_data['email'] = filter_var(trim($_POST['email'] ?? ""), FILTER_VALIDATE_EMAIL);

        // do not make any modifications to the submitted passwords because they may contain special
        // chars and spaces
        $form_data['password'] = $_POST['password'] ?? "";
        $form_data['confirm_password'] = $_POST['confirmPassword'] ?? "";

        return $form_data;
    }

    private function handleFormSubmission(): void
    {
        $form_data = $this->getFormData();

        // create a new client object
        $client = new Client(
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

        // validate all attributes (except password) and store errors
        $this->view_data['errors'] = $client->validate();

        // check if email already exists in database
        if (!empty(Client::getByEmail($client->getEmail()))) {
            $this->view_data['errors']['email'] = "Email already in use";
        }

        // validate plain text password
        $password_errors = Client::validatePlainPassword($form_data['password']);
        if (!empty($password_errors)) {
            $this->view_data['errors']['password'] = $password_errors [0];
        }

        // check if passwords do not match
        if ($form_data['confirm_password'] !== $form_data['password']) {
            $this->view_data['errors']['confirmPassword'] = 'Passwords do not match';
        }

        // if all data valid, save record and redirect to login page
        if (empty($this->view_data['errors'])) {
            $success = $client->save();

            if ($success) {
                Utility::redirect('login');
            }

            // TODO: redirect to some error page
            Utility::redirect('home');
        } else {
            $this->loadDataToForm($form_data);
        }
    }

    /**
     * Updates view data with data from form. Invalid data entered by user persists.
     * @param array $form_data
     * @return void
     */
    private function loadDataToForm(array $form_data): void
    {
        $this->view_data['defaultFirstName'] = $form_data['first_name'];
        $this->view_data['defaultLastName'] = $form_data['last_name'];
        $this->view_data['defaultPhoneNumber'] = $form_data['phone_no'];
        $this->view_data['defaultStreet'] = $form_data['street'];
        $this->view_data['defaultCity'] = $form_data['city'];
        $this->view_data['defaultEmail'] = $form_data['email'];
        $this->view_data['defaultDistrictID'] = $form_data['district'];
        $this->view_data['defaultPassword'] = $form_data['password'];
        $this->view_data['defaultConfirmPassword'] = $form_data['confirm_password'];
    }

    public function index(): void
    {
        if (isset($_POST['form_submit'])) {
            $this->handleFormSubmission();
        }

        $this->view(
            'Register',
            $this->view_data,
            'Register',
            template_meta_description: "Join the Steamy Sips community today. Register for exclusive offers,
             personalized recommendations, and a richer coffee experience. Start your journey towards
              flavorful indulgence."
        );
    }
}
