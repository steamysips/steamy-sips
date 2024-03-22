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
                $data['defaultEmail'],
                $data['defaultFirstName'],
                $data['defaultLastName'],
                $data['defaultPassword'],
                $data['defaultPhoneNumber'],
                new District($data['defaultDistrictID']),
                $data['defaultStreet'],
                $data['defaultCity']
            );

            // validate all attributes
            $data['errors'] = $client->validate();

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
