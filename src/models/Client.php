<?php

namespace Steamy\Model;

use Steamy\Core\Utility;

class Client extends User
{
    protected string $table = 'client';

    private District $district;

    private string $street;

    private string $city;

    public static function getByID(int $id): Client|false
    {
        $query = <<<EOL
        SELECT * FROM user
        INNER JOIN client
        ON user.user_id = client.user_id
        WHERE user.user_id = :user_id;
        EOL;

        Utility::show($query);

        $result = self::get_row($query, array('user_id' => $id));
        Utility::show($result);

        if (!$result) {
            return false;
        }

        return new Client(
            $result->email,
            $result->first_name,
            $result->last_name,
            $result->password,
            $result->phone_no,
            new District($result->district_id),
            $result->street,
            $result->city
        );
    }

    public function save(): void
    {
        // if attributes of object are invalid, exit
        if (count($this->validate()) > 0) {
            Utility::show($this->validate());
            return;
        }

        // get data to be inserted to user table
        $user_data = $this->toArray();
        unset($user_data['user_id']);
        unset($user_data['district']);
        unset($user_data['street']);
        unset($user_data['city']);

        // perform insertion to user table
        $this->insert($user_data, 'user');

        $inserted_record = self::first($user_data, 'user');

        if (!$inserted_record) {
            return;
        }

        // get data to be inserted to client table
        $client_data = (array)[
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

    public function __construct(
        string $email,
        string $first_name,
        string $last_name,
        string $password,
        string $phone_no,
        District $district,
        string $street,
        string $city
    ) {
        parent::__construct($email, $first_name, $last_name, $password, $phone_no);
        $this->district = $district;
        $this->street = $street;
        $this->city = $city;
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