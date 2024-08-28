<?php

declare(strict_types=1);

namespace Steamy\Model;

class Administrator extends User
{
    protected string $table = 'administrator';
    private string $job_title;
    private bool $is_super_admin;

    public function __construct(
        string $email,
        string $first_name,
        string $last_name,
        string $plain_password,
        string $phone_no,
        string $job_title,
        bool $is_super_admin
    ) {
        parent::__construct($email, $first_name, $last_name, $plain_password, $phone_no);
        $this->job_title = $job_title;
        $this->is_super_admin = $is_super_admin;
    }

    /**
     * Returns details of current object as an associative array
     * @return array
     */
    public function toArray(): array
    {
        $base_array = parent::toArray();
        $base_array['job_title'] = $this->job_title;
        $base_array['is_super_admin'] = $this->is_super_admin;
        return $base_array;
    }

    public function validate(): array
    {
        $errors = parent::validate(); // list of errors

        // perform existence checks
        if (strlen($this->job_title) <= 3) {
            $errors['job_title'] = "Job title must be longer than 3 characters";
        }

        return $errors;
    }

    /***
     * Inserts current administrator object to database.
     * @return bool Success or not
     */
    public function save(): bool
    {
        // if attributes of object are invalid, exit
        if (count($this->validate()) > 0) {
            return false;
        }

        // get data to be inserted to user table
        $user_data = $this->toArray();
        unset($user_data['user_id']);
        unset($user_data['job_title']);
        unset($user_data['is_super_admin']);

        // start transaction
        $conn = self::connect();
        $conn->beginTransaction();

        // perform insertion to user table
        $query = <<< EOL
        INSERT INTO user(email, first_name, password, phone_no, last_name) 
        VALUES(:email, :first_name, :password, :phone_no, :last_name);
        EOL;
        $stm = $conn->prepare($query);
        $success = $stm->execute($user_data);


        if (!$success) {
            $conn->rollBack();
            return false;
        }

        $this->user_id = (int)$conn->lastInsertId();

        // perform insertion to administrator table
        $query = <<< EOL
        INSERT INTO administrator(user_id, job_title, is_super_admin)
        VALUES(:user_id, :job_title, :is_super_admin);
        EOL;
        $stm = $conn->prepare($query);
        $success = $stm->execute([
            'user_id' => $this->user_id,
            'job_title' => $this->job_title,
            'is_super_admin' => $this->is_super_admin ? 1 : 0
        ]);

        if (!$success) {
            $conn->rollBack();
            return false;
        }

        $conn->commit();
        $conn = null;
        return true;
    }

    /**
     * Gets a user from database given the user ID.
     * @param int $userId ID of a user (client or administrator).
     * @return User|null User matching ID. Null if no such user exist.
     */
    public static function getById(int $userId): ?User
    {
        $query = <<<EOL
        SELECT * FROM user
        INNER JOIN administrator
        ON user.user_id = administrator.user_id
        WHERE user.user_id = :userId;
        EOL;

        // Execute the query and retrieve the result
        $result = self::get_row($query, ['userId' => $userId]);

        // Check if the result is empty
        if (!$result) {
            return null;
        }

        // Create a new Administrator object
        $administrator = new Administrator(
            email: $result->email,
            first_name: $result->first_name,
            last_name: $result->last_name,
            plain_password: "dummy",
            phone_no: $result->phone_no,
            job_title: $result->job_title,
            is_super_admin: filter_var($result->is_super_admin, FILTER_VALIDATE_BOOLEAN)
        );

        // Set the user ID and password hash
        $administrator->user_id = $result->user_id;
        $administrator->password = $result->password;

        return $administrator;
    }

    /**
     * Returns the Administrator object corresponding to the given email.
     *
     * @param string $email The email of the administrator.
     * @return ?Administrator The Client object if found, otherwise null.
     */
    public static function getByEmail(string $email): ?Administrator
    {
        $query = <<<EOL
        SELECT * FROM user
        INNER JOIN administrator
        ON user.user_id = administrator.user_id
        WHERE user.email = :email;
        EOL;

        // Execute the query and retrieve the result
        $result = self::get_row($query, ['email' => $email]);

        // Check if the result is empty
        if (!$result) {
            return null;
        }

        // Create a new Administrator object
        $administrator = new Administrator(
            email: $email,
            first_name: $result->first_name,
            last_name: $result->last_name,
            plain_password: "dummy",
            phone_no: $result->phone_no,
            job_title: $result->job_title,
            is_super_admin: filter_var($result->is_super_admin, FILTER_VALIDATE_BOOLEAN)
        );

        // Set the user ID and password hash
        $administrator->user_id = $result->user_id;
        $administrator->password = $result->password;

        return $administrator;
    }

    public function updateAdministrator(bool $updatePassword = false): bool
    {
        $conn = self::connect();
        $conn->beginTransaction();

        $user_data = [
            'email' => $this->getEmail(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'phone_no' => $this->getPhoneNo(),
            'user_id' => $this->user_id
        ];

        $query = <<< EOL
        UPDATE user
        SET email = :email,
            first_name = :first_name,
            last_name = :last_name,
            phone_no = :phone_no
    EOL;

        if ($updatePassword) {
            $query .= ", password = :password ";
            $user_data['password'] = $this->password;
        }

        $query .= " WHERE user_id = :user_id";

        $stm = $conn->prepare($query);
        $success = $stm->execute($user_data);

        // if error occurred
        if (!$success) {
            $conn->rollBack();
            $conn = null;
            return false;
        }

        // Update job title and super admin status in the administrator table
        $query = <<< EOL
        UPDATE administrator
        SET job_title = :job_title,
            is_super_admin = :is_super_admin
        WHERE user_id = :user_id
    EOL;

        $stm = $conn->prepare($query);
        $success = $stm->execute([
            'job_title' => $this->job_title,
            'is_super_admin' => $this->is_super_admin ? 1 : 0,
            'user_id' => $this->user_id
        ]);

        // if error occurred
        if (!$success) {
            $conn->rollBack();
            $conn = null;
            return false;
        }

        $conn->commit();
        $conn = null;
        return true;
    }


    public function getJobTitle(): string
    {
        return $this->job_title;
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin;
    }

    public function setJobTitle(string $job_title): void
    {
        $this->job_title = $job_title;
    }

    public function setSuperAdmin(bool $is_super_admin): void
    {
        $this->is_super_admin = $is_super_admin;
    }
}
