<?php

declare(strict_types=1);

namespace Steamy\Controller\API;

use Steamy\Core\Utility;
use Steamy\Model\Review;
use \Steamy\Model\Product as ProductModel;

class Reviews
{

    public static array $routes = [
        'GET' => [
            '/api/v1/reviews' => 'getAllReviews',
            '/api/v1/reviews/{id}' => 'getReviewByID',
            '/api/v1/products/{id}/reviews' => 'getAllReviewsForProduct',
        ],
        'POST' => [
            '/api/v1/reviews' => 'createReview',
        ],
        'PUT' => [
            '/api/v1/reviews/{id}' => 'updateReview',
        ],
        'DELETE' => [
            '/api/v1/reviews/{id}' => 'deleteReview',
        ]
    ];

    /**
     * Get the list of all reviews available.
     */
    public function getAllReviews(): void
    {
        // Retrieve all reviews from the database
        $allReviews = Review::getAll();

        // Convert reviews to array format
        $result = [];
        foreach ($allReviews as $Review) {
            $result[] = $Review->toArray();
        }

        // Return JSON response
        echo json_encode($result);
    }

    public function getReviewByID(): void
    {
        $id = (int)Utility::splitURL()[3];

        // Retrieve all reviews from the database
        $review = Review::getByID($id);

        // Check if product exists
        if ($review === null) {
            // review not found, return 404
            http_response_code(404);
            echo json_encode(['error' => 'Review not found']);
            return;
        }

        // Return JSON response
        echo json_encode($review->toArray());
    }


    /**
     * Get all reviews for a particular product by its ID.
     */
    public function getAllReviewsForProduct(): void
    {
        $productId = (int)Utility::splitURL()[3];
        // Check if product exists
        if (ProductModel::getById($productId) === null) {
            // product not found, return 404
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
            return;
        }

        // Retrieve all reviews for the specified product from the database
        $reviews = Review::getAllReviewsForProduct($productId);

        // Return JSON response
        echo json_encode($reviews);
    }

    /**
     * Create a new review for a product.
     */
    public function createReview(): void
    {
        // Retrieve POST data
        $postData = $_POST;

        // TODO: Implement validation for required fields and data types
        // Check if required fields are present
        $requiredFields = [
            'product_id',
            'client_id',
            'text',
            'rating',
        ];

        if (empty($postData)) {
            http_response_code(400);
            echo json_encode(['error' => "Missing fields: " . implode(', ', $requiredFields)]);
            return;
        }

        foreach ($requiredFields as $field) {
            if (empty($postData[$field])) {
                // Required field is missing, return 400 Bad Request
                http_response_code(400);
                echo json_encode(['error' => "Missing required field: $field"]);
                return;
            }
        }
        // Create a new Review object
        $newReview = new Review(
            null, // review_id will be auto-generated
            (int)$postData['product_id'],
            (int)$postData['client_id'],
            $postData['text'],
            (int)$postData['rating']
        );

        $errors = $newReview->validate();

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['error' => ($errors)]);
            return;
        }

        // Save the new review to the database
        if ($newReview->save()) {
            // Review created successfully, return 201 Created
            http_response_code(201);
            echo json_encode(['message' => 'Review created successfully', 'review_id' => $newReview->getReviewID()]);
        } else {
            // Failed to create review, return 500 Internal Server Error
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create review']);
        }
    }

    /**
     * Update the details of a review with the specified ID.
     */
    public function updateReview(): void
    {
        $reviewId = (int)Utility::splitURL()[3];

        // Retrieve PUT request data
        $putData = json_decode(file_get_contents("php://input"), true);

        // Check if PUT data is valid
        if (empty($putData)) {
            // Invalid JSON data
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        // Retrieve existing review
        $review = Review::getByID($reviewId);

        // Check if review exists
        if ($review === null) {
            // Review not found
            http_response_code(404); // Not Found
            echo json_encode(['error' => 'Review not found']);
            return;
        }

        // Update review in the database
        $success = $review->updateReview($putData);

        if ($success) {
            // Review updated successfully
            http_response_code(200); // OK
            echo json_encode(['message' => 'Review updated successfully']);
        } else {
            // Failed to update review
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to update review']);
        }
    }

    /**
     * Delete a review with the specified ID.
     */
    public function deleteReview(): void
    {
        $reviewId = (int)Utility::splitURL()[3];

        // Retrieve the review by ID
        $review = Review::getByID($reviewId);

        // Check if review exists
        if ($review === null) {
            // Review not found, return 404
            http_response_code(404);
            echo json_encode(['error' => 'Review not found']);
            return;
        }

        // Attempt to delete the review
        if ($review->deleteReview($reviewId)) {
            // Review successfully deleted
            http_response_code(204); // No Content
        } else {
            // Failed to delete the review
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to delete review']);
        }
    }
}
