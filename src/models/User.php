<?php

declare(strict_types=1);

namespace Steamy\Model;

use Steamy\Core\Model;
use Steamy\Core\Database;
use Steamy\Core\Mailer;

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

        // Check if password length is at least 5 characters
        if (strlen($plain_password) < 5) {
            $errors[] = "Password must be at least 5 characters long";
        }

        // Check if password contains at least one uppercase letter
        if (!preg_match('/[A-Z]/', $plain_password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }

        // Check if password contains at least one lowercase letter
        if (!preg_match('/[a-z]/', $plain_password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }

        // Check if password contains at least one digit
        if (!preg_match('/\d/', $plain_password)) {
            $errors[] = "Password must contain at least one digit";
        }

        return $errors;
    }

    /**
     * Validates all attributes of user through existence checks, length checks, format checks, ...
     *
     * Note: This function does not check if an email is unique.
     *
     * @return array Array of errors indexed by attribute name
     */
    public function validate(): array
    {
        $errors = []; // List of errors

        // Perform email format check
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format";
        }

        // Perform first name length check
        if (strlen($this->first_name) < 3) {
            $errors['first_name'] = "First name must be at least 3 characters long";
        }

        // Perform last name length check
        if (strlen($this->last_name) < 3) {
            $errors['last_name'] = "Last name must be at least 3 characters long";
        }

        // Perform phone number length check
        if (strlen($this->phone_no) < 7) {
            $errors['phone_no'] = "Phone number must be at least 7 characters long";
        }

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

    public static function getUserIdByEmail(string $email): ?int
    {
        //Implement logic to fetch user ID by email from the database
        $query = "SELECT user_id FROM user WHERE email = :email";
        $result = Database::query($query, ['email' => $email]);
        return $result[0]['user_id'] ?? null;
    }

    public static function savePasswordChangeRequest(int $userId, string $tokenHash, string $expiryDate)
    {
        //Implement logic to save password change request in the database
        $query = "INSERT INTO password_change_request (user_id, token_hash, expiry_date) VALUES (:userId, :tokenHash, :expiryDate)";
        Database::query($query, ['userId' => $userId, 'tokenHash' => $tokenHash, 'expiryDate' => $expiryDate]);
        
    }

    public static function sendResetEmail(string $email, string $resetLink)
    {
        //Implement logic to send reset email using Mailer class
         $mailer = new Mailer();
         $subject = "Reset Your Password";
        $htmlMessage = "Click the link below to reset your password:<br><a href='$resetLink'>$resetLink</a>";
        $plainMessage = "Click the link below to reset your password:\n$resetLink";
        $mailer->sendMail($email, $subject, $htmlMessage, $plainMessage);
    }

    public static function getUserIdByToken(string $token): ?int
{
    //Implement logic to fetch user ID by token from the database
    $query = "SELECT user_id FROM password_change_request WHERE token_hash = :token AND expiry_date > NOW() AND used = false";
    $result = Database::query($query, ['token' => $token]);
    return $result[0]['user_id'] ?? null;
}

public static function updatePassword(int $userId, string $hashedPassword): void
{
    // Implement logic to update user's password in the database
    $query = "UPDATE user SET password = :password WHERE user_id = :userId";
    Database::query($query, ['password' => $hashedPassword, 'userId' => $userId]);
}

}
