<?php

declare(strict_types=1);

namespace Steamy\Model;

class Client extends User
{
    protected string $table = 'client';
    private District $district; // name of district where client lives
    private string $street; // name of street where client lives
    private string $city; // name of city where client lives

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
     * Returns a Client object based on a specified condition (email or user ID).
     * If the user is not found, returns false.
     *
     * @param string|null $email The email of the client. If null, the user ID will be used.
     * @param int|null $user_id The user ID of the client. If null, the email will be used.
     * @return Client|false The Client object if found, otherwise false.
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
        WHERE {$condition};
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
            new District($result->district_id),
            $result->street,
            $result->city
        );

        // Set the user ID and password hash
        $client->setUserID($result->user_id);
        $client->setPassword($result->password);

        return $client;
    }

    /**
     * Returns a Client object for a given email. If the email is not found, returns false.
     *
     * @param string $email The email of the client.
     * @return Client|false The Client object if found, otherwise false.
     */
    public static function getByEmail(string $email): ?Client
    {
        return self::getClientByCondition($email, null);
    }

    /**
     * Returns a Client object for a given user ID. If the user ID is not found, returns false.
     *
     * @param int $user_id The ID of the user/client.
     * @return Client|false The Client object if found, otherwise false.
     */
    public static function getByID(int $user_id): ?Client
    {
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
     * Saves user to database if user attributes are valid
     *
     * @return void
     */
    public function save(): void
    {
        // if attributes of object are invalid, exit
        if (count($this->validate()) > 0) {
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

    /**
     * Returns address of user
     * @return string String containing street name, city name, and district name
     */
    public function getAddress(): string
    {
        return ucfirst($this->street) . ", " . ucfirst($this->city) . ", " . $this->district->getName();
    }

    /**
     * Validates attributes of current user and returns an array of errors.
     *
     * @return array Associative array indexed by attribute name containing errors
     */
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

    /**
     * Converts user object to an array
     * @return array Associative array indexed by attribute name
     */
    public function toArray(): array
    {
        $base_array = parent::toArray();
        $base_array['district'] = $this->district->getName();
        $base_array['street'] = $this->street;
        $base_array['city'] = $this->city;

        return $base_array;
    }
}