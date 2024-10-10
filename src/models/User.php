<?php

declare(strict_types=1);

namespace Steamy\Model;

use Exception;
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

    /**
     * @return User[] A list of all users in database
     */
    public static function getUsers(): array
    {
        return []; // TODO: Fetch all client and all administrators
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
     * @param string $plain_password New password in plain format
     * @return void
     */
    public function setPassword(string $plain_password): void
    {
        $this->password = password_hash($plain_password, PASSWORD_BCRYPT);
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

    public static function getFullName(int $user_id): ?string
    {
        $query = "select first_name, last_name from user where user_id = :user_id ";
        $result = self::query($query, ['user_id' => $user_id]);
        if (empty($result)) {
            return null;
        }
        $result = $result[0];

        return $result->first_name . " " . $result->last_name;
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
        $result = self::query($query, ['email' => $email]);

        if (empty($result)) {
            return null;
        }
        return $result[0]->user_id;
    }

    /**
     * Generates a password reset token for a user. A hash of the token
     * is stored in the database.
     *
     * @param int $userId ID of user to which password reset token belongs
     * @return array|null An associative array with attributes `request_id` and `token`
     * @throws Exception
     */
    public static function generatePasswordResetToken(int $userId): ?array
    {
        if ($userId < 0) {
            return null;
        }

        $info = []; // array to store token and request ID

        // Generate random token and its associated information
        $info['token'] = "";
        try {
            $info['token'] = bin2hex(random_bytes(16)); // length 32 bytes (hexadecimal format)
        } catch (Exception) {
            throw new Exception('Token cannot be generated');
        }

        $expiryDate = date('Y-m-d H:i:s', strtotime('+1 day')); // Expiry date set to 1 day from now
        $tokenHash = password_hash($info['token'], PASSWORD_BCRYPT); // Hashing the token for security

        // Save token info to database
        $query = "INSERT INTO password_change_request (user_id, token_hash, expiry_date)
                  VALUES (:userId, :tokenHash, :expiryDate)";
        $conn = self::connect();
        $stm = $conn->prepare($query);
        $stm->execute(['userId' => $userId, 'tokenHash' => $tokenHash, 'expiryDate' => $expiryDate]);

        // Check if insertion was unsuccessful
        if ($stm->rowCount() === 0) {
            $conn = null;
            return null;
        }

        // get ID of last inserted record
        $info['request_id'] = (int)$conn->lastInsertId();

        return $info;
    }


    /**
     * Uses a password reset token to change the password of a user.
     * @param int $requestID request ID corresponding to password reset token
     * @param string $token Password reset token
     * @param string $new_password New password of user in plain text (not hashed)
     * @return bool
     */
    public static function resetPassword(int $requestID, string $token, string $new_password): bool
    {
        // fetch matching request from database if valid (not expired and unused)
        $query = <<< EOL
        SELECT * FROM password_change_request
        WHERE expiry_date > NOW() AND used = 0 AND request_id = :requestID
        EOL;

        $result = self::query($query, ['requestID' => $requestID]);

        if (empty($result)) {
            return false;
        }

        $request = $result[0];

        // verify token
        if (!password_verify($token, $request->token_hash)) {
            return false;
        }

        // validate password
        if (!empty(self::validatePlainPassword($new_password))) {
            return false;
        }

        $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);

        // Update user's password in the database
        $query = "UPDATE user SET password = :password WHERE user_id = :userId";
        self::query($query, ['password' => $hashedPassword, 'userId' => $request->user_id]);


        // Invalidate password request token so that token cannot be used again
        $query = "UPDATE password_change_request SET used = 1 WHERE request_id = :requestID";
        self::query($query, ['requestID' => $request->request_id]);

        return true;
    }

    /**
     * Get all reviews written by user.
     *
     * @return Review[] Array of Review objects
     */
    public function getReviews(): array
    {
        return []; // TODO: Implement getReviews()
    }

    /**
     * Deletes a user record from database.
     * @return void
     */
    public function deleteUser(): void
    {
        if ($this instanceof Client) {
            // delete record from client table
            $this->delete($this->user_id, 'client', 'user_id');
        } elseif ($this instanceof Administrator) {
            // delete record from administrator table
            $this->delete($this->user_id, 'administrator', 'user_id');
        }

        // delete record from user table
        $this->delete($this->user_id, 'user', 'user_id');
    }
}
