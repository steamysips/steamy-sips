<?php

class User
{
    use Model;

    protected string $table = 'user';
    private string $name;
    private string $password;

    public function __construct(string $name = '', string $password = '')
    {
        $this->name = $name;
        $this->password = $password;
    }

    // sanitize

    /**
     * Validates user properties
     * @param $data array data with name and password properties
     * @return bool True if data passes all checks
     */
    public function validate(array $data): bool
    {
        $this->errors = [];

        if (empty($data['name'])) {
            $this->errors['name'] = "Name is required";
        }

        if (strlen($data['name']) < 5) {
            $this->errors['name'] = "Name too short";
        }

        if (empty($data['password'])) {
            $this->errors['password'] = "Password is required";
        }

        if (strlen($data['password']) < 5) {
            $this->errors['password'] = "Password too short";
        }

        if (array_key_exists('confirmPassword', $data) && $data['password'] != $data['confirmPassword']) {
            $this->errors['confirmPassword'] = "Passwords do not match";
        }

        return (empty($this->errors));
    }

    // ! Override insert and use bcrypt
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
