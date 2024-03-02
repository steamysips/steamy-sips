<?php

namespace Steamy\Model;

use Steamy\Core\Model;

abstract class User
{
    use Model;

    protected string $table = 'user';
    protected int $user_id = -1;
    protected string $email = "";
    protected string $first_name = "";
    protected string $last_name = "";
    protected string $password = "";
    protected string $phone_no = "";

    public function __construct(
        string $email,
        string $first_name,
        string $last_name,
        string $password,
        string $phone_no
    ) {
        $this->email = $email;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->password = password_hash($password, PASSWORD_BCRYPT);;
        $this->phone_no = $phone_no;
    }

    public function toArray(): array
    {
        return
            (array)[
                'user_id' => $this->user_id,
                'email' => $this->email,
                'first_name' => $this->first_name,
                'password' => $this->password,
                'phone_no' => $this->phone_no,
                'last_name' => $this->last_name
            ];
    }

    public function validate()
    {
        // validate current object
    }

    public function save()
    {
        // if errors present exit
        if (count($this->validate()) > 0) {
            return;
        }

        // else insert current object to database
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setFirstName(string $first_name): void
    {
        $this->first_name = $first_name;
    }

    public function setLastName(string $last_name): void
    {
        $this->last_name = $last_name;
    }

    public function setPassword(string $original_password): void
    {
        $this->password = password_hash($original_password, PASSWORD_BCRYPT);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function getPhoneNo(): string
    {
        return $this->phone_no;
    }

    public function getFullName(): string
    {
        return "$this->first_name $this->last_name";
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
