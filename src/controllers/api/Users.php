<?php

declare(strict_types=1);

namespace Steamy\Controller\API;

use Opis\JsonSchema\{Errors\ErrorFormatter};
use Steamy\Core\Utility;
use Steamy\Model\User;
use Steamy\Model\Location;
use Steamy\Model\Client;
use Steamy\Model\Administrator;

class Users
{
    public static array $routes = [
        'GET' => [
            '/users' => 'getAllUsers',
            '/users/{id}' => 'getUserById',
            '/users/{id}/orders' => 'getAllOrdersForUser',
            '/users/{id}/reviews' => 'getAllReviewsForUser',
        ],
        'POST' => [
            '/users' => 'createUser',
        ],
        'PUT' => [
            '/users/{id}' => 'updateUser',
        ],
        'DELETE' => [
            '/users/{id}' => 'deleteUser',
        ]
    ];

    /**
     * Get the list of all users.
     */
    public function getAllUsers(): void
    {
        // Retrieve all users from the database
        $allUsers = User::getUsers();

        // Convert users to array format and remove password field
        $result = [];
        foreach ($allUsers as $user) {
            $userData = $user->toArray();
            unset($userData['password']);
            $result[] = $userData;
        }

        // Return JSON response
        echo json_encode($result);
    }

    /**
     * Get the details of a specific user by their ID.
     */
    public function getUserById(): void
    {
        $userId = (int)Utility::splitURL()[3];

        // Retrieve user details from the database. user is either a client or an administrator
        $user = Client::getById($userId) ?? Administrator::getById($userId);

        if ($user === null) {
            // User not found, return 404
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }

        // Remove password field
        $userData = $user->toArray();
        unset($userData['password']);

        // Return JSON response
        echo json_encode($userData);
    }

    /**
     * Create a new user entry in the database.
     */
    public function createUser(): void
    {
        $data = (object)json_decode(file_get_contents("php://input"), true);

        // Determine if the user to be created is an Administrator
        $isAdministrator = isset($data->job_title) && isset($data->is_super_admin);

        $schemaPath = $isAdministrator ? "administrators/create.json" : "clients/create.json";
        $result = Utility::validateAgainstSchema($data, $schemaPath);

        if (!($result->isValid())) {
            $errors = (new ErrorFormatter())->format($result->error());
            $response = [
                'error' => $errors
            ];
            http_response_code(400);
            echo json_encode($response);
            return;
        }

        // Create a new User object
        if ($isAdministrator) {
            $newUser = new Administrator(
                $data->email,
                $data->first_name,
                $data->last_name,
                $data->password,
                $data->phone_no,
                $data->job_title,
                $data->is_super_admin
            );
        } else {
            $address = new Location(
                $data->street,
                $data->city,
                $data->district_id
            );
            $newUser = new Client(
                $data->email,
                $data->first_name,
                $data->last_name,
                $data->password,
                $data->phone_no,
                $address
            );
        }

        // Save the new user to the database
        if ($newUser->save()) {
            // User created successfully, return 201 Created
            http_response_code(201);
            echo json_encode(['message' => 'User created successfully', 'user_id' => $newUser->getUserID()]);
        } else {
            // Failed to create user, return 500 Internal Server Error
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create user']);
        }
    }

    /**
     * Delete a user with the specified ID.
     */
    public function deleteUser(): void
    {
        $userId = (int)Utility::splitURL()[3];

        // Retrieve the user by ID
        $user = Client::getById($userId) ?? Administrator::getById($userId);

        // Check if user exists
        if ($user === null) {
            // User not found, return 404
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }

        // Attempt to delete the user
        if ($user instanceof Client || $user instanceof Administrator) {
            $user->deleteUser();
            http_response_code(204); // No Content
        } else {
            // User is neither Client nor Administrator
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid user type']);
        }
    }

    /**
     * Update the details of a user with the specified ID.
     */
    public function updateUser(): void
    {
        $userId = (int)Utility::splitURL()[3];

        // Retrieve the user by ID
        $user = Client::getById($userId) ?? Administrator::getById($userId);

        // Check if user exists
        if ($user === null) {
            // User not found, return 404
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }

        // Retrieve PUT request data
        $data = (object)json_decode(file_get_contents("php://input"), true);

        // Determine the schema to use for validation
        $schema = $user instanceof Administrator ? "administrators/update.json" : "clients/update.json";

        $result = Utility::validateAgainstSchema($data, $schema);

        if (!($result->isValid())) {
            $errors = (new ErrorFormatter())->format($result->error());
            $response = [
                'error' => $errors
            ];
            http_response_code(400);
            echo json_encode($response);
            return;
        }

        // TODO: use $data to update $user then update

        $user->updateUser();
    }

    /**
     * Get all orders for a particular client by their ID.
     */
    public function getAllOrdersForUser(): void
    {
        $userId = (int)Utility::splitURL()[3];

        // Check if user exists
        $client = Client::getByID($userId);
        if ($client === null) {
            // User not found, return 404
            http_response_code(404);
            echo json_encode(['error' => 'Client not found']);
            return;
        }

        // Retrieve all orders for the specified user from the database
        $orders = $client->getOrders();

        // Convert orders to array format
        $result = [];
        foreach ($orders as $order) {
            $result[] = $order->toArray();
        }

        echo json_encode($result);
    }

    /**
     * Get all reviews for a particular user by their ID.
     */
    public function getAllReviewsForUser(): void
    {
        $userId = (int)Utility::splitURL()[3];

        $user = Client::getById($userId) ?? Administrator::getById($userId);

        // Check if user exists
        if ($user === null) {
            // User not found, return 404
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }

        // Retrieve all reviews for the specified user from the database
        $reviews = $user->getReviews();

        // Return JSON response
        echo json_encode($reviews);
    }
}
