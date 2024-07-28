<?php

declare(strict_types=1);

namespace Steamy\Controller\API;

use Opis\JsonSchema\{Errors\ErrorFormatter};
use Exception;
use Steamy\Core\Utility;
use Steamy\Model\Review;

class Reviews
{

    public static array $routes = [
        'GET' => [
            '/reviews' => 'getAllReviews',
            '/reviews/{id}' => 'getReviewByID',
        ],
        'POST' => [
            '/reviews' => 'createReview',
        ],
        'PUT' => [
            '/reviews/{id}' => 'updateReview',
        ],
        'DELETE' => [
            '/reviews/{id}' => 'deleteReview',
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
     * Create a new review for a product.
     */
    public function createReview(): void
    {
        // Retrieve POST data
        $data = (object)json_decode(file_get_contents("php://input"), true);

        // Validate against JSON schema
        $result = Utility::validateAgainstSchema($data, "reviews/create.json");

        if (!($result->isValid())) {
            $errors = (new ErrorFormatter())->format($result->error());
            $response = [
                'error' => $errors
            ];
            http_response_code(400);
            echo json_encode($response);
            return;
        }

        // Create a new Review object
        $newReview = new Review(
            null, // review_id will be auto-generated
            (int)$data->product_id,
            (int)$data->client_id,
            $data->text,
            (int)$data->rating
        );

        // Save the new review to the database
        try {
            $newReview->save();
            // Review created successfully, return 201 Created
            http_response_code(201);
            echo json_encode(['message' => 'Review created successfully', 'review_id' => $newReview->getReviewID()]
            );
        } catch (Exception $e) {
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
        $data = (object)json_decode(file_get_contents("php://input"), true);

        // Validate against JSON schema
        $result = Utility::validateAgainstSchema($data, "reviews/update.json");

        if (!($result->isValid())) {
            $errors = (new ErrorFormatter())->format($result->error());
            $response = [
                'error' => $errors
            ];
            http_response_code(400);
            echo json_encode($response);
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
        $success = $review->updateReview((array)$data);

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
        if ($review->deleteReview()) {
            // Review successfully deleted
            http_response_code(204); // No Content
        } else {
            // Failed to delete the review
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to delete review']);
        }
    }
}
