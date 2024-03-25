<?php

declare(strict_types=1);

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
    protected string $password = ""; // hash of original password
    protected string $phone_no = "";

    /**
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @param string $plain_password plain (original) version of password
     * @param string $phone_no
     */
    public function __construct(
        string $email,
        string $first_name,
        string $last_name,
        string $plain_password,
        string $phone_no
    ) {
        $this->email = $email;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->password = password_hash($plain_password, PASSWORD_BCRYPT);
        $this->phone_no = $phone_no;
    }

    public function toArray(): array
    {
        return
            [
                'user_id' => $this->user_id,
                'email' => $this->email,
                'first_name' => $this->first_name,
                'password' => $this->password,
                'phone_no' => $this->phone_no,
                'last_name' => $this->last_name
            ];
    }

    /**
     * Validate plain password.
     *
     * @param string $plain_password The plain text password to validate.
     * @return string[] List of all errors.
     */
    public static function validatePlainPassword(string $plain_password): array
    {
        $errors = []; // List of errors

        // Check if password is empty
        if (empty($plain_password)) {
            $errors[] = "Password is required";
        }

        // Check if password length is within acceptable limits
        $min_length = 1;
        $max_length = 32;
        $password_length = strlen($plain_password);
        if ($password_length < $min_length || $password_length > $max_length) {
            $errors[] = "Password must be between $min_length and $max_length characters long";
        }

        // TODO: Add more validation checks here, such as checking for specific characters,
        // enforcing complexity requirements, etc.

        return $errors;
    }

    public function validate(): array
    {
        $errors = []; // list of errors

        // perform existence checks
        if (empty($this->email)) {
            $errors['email'] = "Email is required";
        }

        if (empty($this->first_name)) {
            $errors['first_name'] = "First name is required";
        }

        if (empty($this->phone_no)) {
            $errors['phone_no'] = "Phone number is required";
        }

        if (empty($this->last_name)) {
            $errors['last_name'] = "Last name is required";
        }

        // TODO: Add range checks
        return $errors;
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

    /**
     * Changes the hash of the user password
     * @param string $new_password_hash new password hash
     * @return void
     */
    public function setPassword(string $new_password_hash): void
    {
        $this->password = $new_password_hash;
    }

    /**
     * Checks if the password hash of the user matches parameter
     * @param string $plain_password
     * @return bool
     */
    public function verifyPassword(string $plain_password): bool
    {
        return password_verify($plain_password, $this->password);
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

    public function getUserID(): int
    {
        return $this->user_id;
    }

    public function setUserID(int $new_id): void
    {
        $this->user_id = $new_id;
    }
}
