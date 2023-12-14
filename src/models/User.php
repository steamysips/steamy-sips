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
        if (empty($data['password'])) {
            $this->errors['password'] = "Password is required";
        }

        if (empty($this->errors)) return true;
        return false;
    }
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
