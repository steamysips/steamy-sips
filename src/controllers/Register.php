<?php

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Client;
use Steamy\Model\District;

class Register
{
    use Controller;

    public function index(): void
    {
        $data['defaultFirstName'] = "";
        $data['defaultLastName'] = "";
        $data['defaultPhoneNumber'] = "";
        $data['defaultDistrictID'] = 7;
        $data['defaultStreet'] = "";
        $data['defaultCity'] = "";
        $data['defaultEmail'] = "";
        $data['defaultPassword'] = "";
        $data['defaultConfirmPassword'] = "";

        if (isset($_POST['register_submit'])) {
            // sanitize data
            // update default form values
            $data['defaultFirstName'] = $_POST['first_name'] ?? "";
            $data['defaultLastName'] = $_POST['last_name'] ?? "";
            $data['defaultPhoneNumber'] = $_POST['phone_no'] ?? "";
            $data['defaultDistrictID'] = $_POST['district'] ?? 1;
            $data['defaultStreet'] = $_POST['street'] ?? "";
            $data['defaultCity'] = $_POST['city'] ?? "";
            $data['defaultEmail'] = $_POST['email'] ?? "";
            $data['defaultPassword'] = $_POST['password'] ?? "";
            $data['defaultConfirmPassword'] = $_POST['confirmPassword'] ?? "";

            // create a new client object
            $client = new Client(
                email: $data['defaultEmail'],
                first_name: $data['defaultFirstName'],
                last_name: $data['defaultLastName'],
                plain_password: $data['defaultPassword'],
                phone_no: $data['defaultPhoneNumber'],
                district: new District($data['defaultDistrictID']),
                street: $data['defaultStreet'],
                city: $data['defaultCity']
            );

            // validate all attributes, except password
            $data['errors'] = $client->validate();

            // check if email already exists
            if (!empty(Client::getByEmail($client->getEmail()))) {
                $data['errors']['email'] = "Email already in use";
            }

            // validate plain text password
            $password_errors = Client::validatePlainPassword($data['defaultPassword']);
            if (!empty($password_errors)) {
                $data['errors']['password'] = $password_errors [0];
            }

            // check if passwords do not match
            if ($data['defaultConfirmPassword'] !== $data['defaultPassword']) {
                $data['errors']['confirmPassword'] = 'Passwords do not match';
            }

            // if all data valid, save new record and redirect to login page
            if (empty($data['errors'])) {
                $client->save();
                Utility::redirect('login');
            }
        }

        $this->view(
            'Register',
            $data,
            'Register'
        );
    }
}
