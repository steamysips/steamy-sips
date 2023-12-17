<?php

class User
{
    use Model;
    protected $table = 'user';
    private string $name;
    private string $password;

    public function __construct(string $name = '', string $password = '')
    {
        $this->name = $name;
        $this->password = $password;
    }

    public function validate($data)
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

        if (empty($this->errors)) return true;
        return false;
    }

    // ! Override insert and use bcrypt
    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name)
    {
        $this->name = $name;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function setPassword(string $password)
    {
        $this->password = $password;
    }
}
