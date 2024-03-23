<?php

namespace Steamy\Model;

use Steamy\Core\Utility;

class Client extends User
{
    protected string $table = 'client';
    private District $district;
    private string $street;
    private string $city;

    public function __construct(
        string $email,
        string $first_name,
        string $last_name,
        string $plain_password,
        string $phone_no,
        District $district,
        string $street,
        string $city
    ) {
        parent::__construct($email, $first_name, $last_name, $plain_password, $phone_no);
        $this->district = $district;
        $this->street = $street;
        $this->city = $city;
    }

    /**
     * Returns a Client object for a given email. If email not found,
     * false is returned.
     *
     * @param string $email email of client
     * @return Client|false
     */
    public static function getByEmail(string $email): Client|false
    {
        $query = <<<EOL
        SELECT * FROM user
        INNER JOIN client
        ON user.user_id = client.user_id
        WHERE user.email = :email;
        EOL;

        $result = self::get_row($query, array("email" => $email));

        if (!$result) {
            return false;
        }

        $client = new Client(
            $result->email,
            $result->first_name,
            $result->last_name,
            "dummy-password", // a dummy is used since original password is unknown
            $result->phone_no,
            new District($result->district_id),
            $result->street,
            $result->city
        );

        // store hash of true password
        $client->setPassword($result->password);

        return $client;
    }

    public function save(): void
    {
        // if attributes of object are invalid, exit
        if (count($this->validate()) > 0) {
            Utility::show($this->validate());
            return;
        }

        // get data to be inserted to user table
        $user_data = parent::toArray();
        unset($user_data['user_id']);

        // perform insertion to user table
        $this->insert($user_data, 'user');

        $inserted_record = self::first($user_data, 'user');

        if (!$inserted_record) {
            return;
        }

        // get data to be inserted to client table
        $client_data = [
            'user_id' => $inserted_record->user_id,
            'street' => $this->street,
            'city' => $this->city,
            'district_id' => $this->district->getID()
        ];

        // perform insertion to client table
        $this->insert($client_data, $this->table);
    }

    public function validate(): array
    {
        $errors = parent::validate(); // list of errors

        // perform existence checks
        if (empty($this->district->getName())) {
            $errors['district'] = 'District name is required';
        }

        if (empty($this->city)) {
            $errors['city'] = 'City name is required';
        }

        if (empty($this->street)) {
            $errors['street'] = 'Street name is required';
        }

        return $errors;
    }

    public function toArray(): array
    {
        $base_array = parent::toArray();
        $base_array['district'] = $this->district->getName();
        $base_array['street'] = $this->street;
        $base_array['city'] = $this->city;

        return $base_array;
    }
    // TODO: Add getters + setters
}