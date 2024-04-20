<?php

declare(strict_types=1);

namespace Steamy\Model;

use Steamy\Core\Utility;

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
        $base_array['is_super_admin'] = $this->is_super_admin ? 1 : 0;
        return $base_array;
    }

    public function validate(): array
    {
        $errors = parent::validate(); // list of errors

        // perform existence checks
        if (empty($this->job_title)) {
            $errors['job_title'] = "Job title is required";
        }

        if (strlen($this->job_title) <= 3) {
            $errors['job_title'] = "Job title must be longer than 3 characters";
        }

        return $errors;
    }

    /***
     * Inserts current administrator object to database.
     * @return void
     */
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
        unset($user_data['job_title']);
        unset($user_data['is_super_admin']);

        // perform insertion to user table
        $this->insert($user_data, 'user');

        $inserted_record = self::first($user_data, 'user');

        if (!$inserted_record) {
            return;
        }

        // get data to be inserted to administrator table
        $admin_data = [
            'user_id' => $inserted_record->user_id,
            'job_title' => $this->job_title,
            'is_super_admin' => $this->is_super_admin ? 1 : 0
        ];

        // perform insertion to administrator table
        $this->insert($admin_data, $this->table);
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
            is_super_admin: filter_var($result->is_super_admin, FILTER_SANITIZE_NUMBER_INT)
        );

        // Set the user ID and password hash
        $administrator->user_id = $result->user_id;
        $administrator->password = $result->password;

        return $administrator;
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