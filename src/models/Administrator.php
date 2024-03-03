<?php

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
        string $password,
        string $phone_no,
        string $job_title,
        bool $is_super_admin
    ) {
        parent::__construct($email, $first_name, $last_name, $password, $phone_no);
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

        // TODO: Add range checks
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

//        utility::show("inserted record: ");
//        Utility::show($inserted_record);

        if (!$inserted_record) {
            return;
        }

        // get data to be inserted to administrator table
        $admin_data = (array)[
            'user_id' => $inserted_record->user_id,
            'job_title' => $this->job_title,
            'is_super_admin' => $this->is_super_admin ? 1 : 0
        ];

        // perform insertion to administrator table
        $this->insert($admin_data, $this->table);
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

    public function setSuperAdmin(string $is_super_admin): void
    {
        $this->is_super_admin = $is_super_admin;
    }
}