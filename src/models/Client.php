<?php

declare(strict_types=1);

namespace Steamy\Model;

class Client extends User
{
    protected string $table = 'client';
    private Location $address;

    public function __construct(
        string $email,
        string $first_name,
        string $last_name,
        string $plain_password,
        string $phone_no,
        Location $address,
    ) {
        parent::__construct($email, $first_name, $last_name, $plain_password, $phone_no);
        $this->address = $address;
    }

    /**
     * Returns a Client object based on a specified condition (email or user ID).
     *
     * @param string|null $email The email of the client. If null, the user ID will be used.
     * @param int|null $user_id The user ID of the client. If null, the email will be used.
     * @return ?Client The Client object if found, otherwise null.
     */
    private static function getClientByCondition(?string $email, ?int $user_id): ?Client
    {
        // Determine the condition to use (email or user ID)
        $condition = $email !== null ? 'user.email = :email' : 'user.user_id = :user_id';
        $params = $email !== null ? ['email' => $email] : ['user_id' => $user_id];

        // Construct the SQL query
        $query = <<<EOL
        SELECT * FROM user
        INNER JOIN client
        ON user.user_id = client.user_id
        WHERE $condition;
        EOL;

        // Execute the query and retrieve the result
        $result = self::get_row($query, $params);

        // Check if the result is empty
        if (!$result) {
            return null;
        }

        // Create a new Client object
        $client = new Client(
            $result->email,
            $result->first_name,
            $result->last_name,
            "dummy-password", // A dummy password is used since the original password is unknown
            $result->phone_no,
            new Location(street: $result->street, city: $result->city, district_id: $result->district_id)
        );

        // Set the user ID and password hash
        $client->user_id = $result->user_id;
        $client->password = $result->password;

        return $client;
    }

    /**
     * Returns a Client object for a given email.
     *
     * @param string $email The email of the client.
     * @return ?Client The Client object if found, otherwise null.
     */
    public static function getByEmail(string $email): ?Client
    {
        if (strlen($email) < 3) {
            // email must have at least 3 characters
            // Ref: https://stackoverflow.com/a/1423203/17627866
            return null;
        }
        return self::getClientByCondition($email, null);
    }

    /**
     * Returns a Client object for a given user ID.
     *
     * @param int $user_id The ID of the user/client.
     * @return Client|false The Client object if found, otherwise false.
     */
    public static function getByID(int $user_id): ?Client
    {
        if ($user_id < 0) {
            // user id cannot be negative
            return null;
        }
        return self::getClientByCondition(null, $user_id);
    }

    /**
     * Deletes user from database
     *
     * @return void
     */
    public function deleteUser(): void
    {
        // delete record from client table
        $this->delete($this->user_id, 'client', 'user_id');

        // delete record from user table
        $this->delete($this->user_id, 'user', 'user_id');
    }

    /**
     * Saves client to database and updates client_id.
     *
     * @return bool Whether client was successfully saved to database
     */
    public function save(): bool
    {
        // if attributes are invalid, exit
        if (count($this->validate()) > 0) {
            return false;
        }

        // check if email already exists in database
        if (!empty(Client::getByEmail($this->email))) {
            return false;
        }

        // get data to be inserted to user table
        $user_data = parent::toArray();
        unset($user_data['user_id']);

        // perform insertion to user table
        $inserted_id = $this->insert($user_data, 'user');

        if ($inserted_id === null) {
            return false;
        }

        $this->user_id = $inserted_id;

        // get data to be inserted to client table
        $client_data = [
            'user_id' => $this->user_id,
            'street' => $this->address->getStreet(),
            'city' => $this->address->getCity(),
            'district_id' => $this->address->getDistrictID()
        ];

        // perform insertion to client table
        $this->insert($client_data, $this->table);

        return true; // insertion was successful
    }

    /**
     * Returns address of user
     * @return Location Object containing street name, city name, and district name
     */
    public function getAddress(): Location
    {
        return $this->address;
    }

    /**
     * Validates attributes of current user and returns an array of errors.
     *
     * @return array Associative array indexed by attribute name containing errors
     */
    public function validate(): array
    {
        $errors = parent::validate(); // list of errors

        // verify existence of district
        $valid_district = $this->address->getDistrict(); // fetch corresponding district in database
        if (empty($valid_district)) {
            $errors['district'] = 'District does not exist';
        }

        if (strlen($this->address->getCity()) < 3) {
            $errors['city'] = 'City name must have at least 3 characters';
        }

        if (strlen($this->address->getStreet()) < 4) {
            $errors['street'] = 'Street name must have at least 4 characters';
        }

        return $errors;
    }

    /**
     * Converts user object to an array
     * @return array Associative array indexed by attribute name
     */
    public function toArray(): array
    {
        $base_array = parent::toArray();
        $base_array['district_id'] = $this->address->getDistrictID();
        $base_array['street'] = $this->address->getStreet();
        $base_array['city'] = $this->address->getCity();

        return $base_array;
    }
}