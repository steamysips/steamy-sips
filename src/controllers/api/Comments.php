<?php

declare(strict_types=1);

namespace Steamy\Controller\API;

use Opis\JsonSchema\{Errors\ErrorFormatter};
use Steamy\Core\Utility;
use Steamy\Model\Comment;
use Steamy\Core\Model;

class Comments
{
    use Model;

    public static array $routes = [
        'GET' => [
            '/comments' => 'getAllComments',
            '/comments/{id}' => 'getCommentById',
        ],
        'POST' => [
            '/comments' => 'createComment',
        ],
        'PUT' => [
            '/comments/{id}' => 'updateComment',
        ],
        'DELETE' => [
            '/comments/{id}' => 'deleteComment',
        ]
    ];

    /**
     * Get the list of all comments.
     */
    public function getAllComments(): void
    {
        $allComments = Comment::getAll();

        $result = [];
        foreach ($allComments as $comment) {
            $result[] = $comment->toArray();
        }

        echo json_encode($result);
    }

    /**
     * Get the details of a specific comment by its ID.
     */
    public function getCommentById(): void
    {
        $commentId = (int)Utility::splitURL()[3];

        $comment = Comment::getByID($commentId);

        if ($comment === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Comment not found']);
            return;
        }

        echo json_encode($comment->toArray());
    }

    /**
     * Create a new comment.
     */
    public function createComment(): void
    {
        $data = (object)json_decode(file_get_contents("php://input"), true);

        // Validate input data against create.json schema
        $result = Utility::validateAgainstSchema($data, "comments/create.json");

        if (!($result->isValid())) {
            $errors = (new ErrorFormatter())->format($result->error());
            $response = [
                'error' => $errors
            ];
            http_response_code(400);
            echo json_encode($response);
            return;
        }

        // Create a new Comment object
        $newComment = new Comment(
            user_id: $data->user_id,
            review_id: $data->review_id,
            parent_comment_id: $data->parent_comment_id ?? null,
            text: $data->text
        );

        // Save the new Comment to the database
        if ($newComment->save()) {
            // Comment created successfully, return 201 Created
            http_response_code(201);
            echo json_encode(['message' => 'Comment created successfully', 'comment_id' => $newComment->getCommentID()]
            );
        } else {
            // Failed to create comment, return 500 Internal Server Error
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create comment']);
        }
    }

    /**
     * Update the details of a comment with the specified ID.
     */
    public function updateComment(): void
    {
        $commentId = (int)Utility::splitURL()[3];

        $comment = Comment::getByID($commentId);

        if ($comment === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Comment not found']);
            return;
        }

        $data = (object)json_decode(file_get_contents("php://input"), true);

        // Validate input data against update.json schema
        $result = Utility::validateAgainstSchema($data, "comments/update.json");

        if (!($result->isValid())) {
            $errors = (new ErrorFormatter())->format($result->error());
            $response = [
                'error' => $errors
            ];
            http_response_code(400);
            echo json_encode($response);
            return;
        }

        // Update comment in the database
        $success = $comment->updateComment((array)$data);

        if ($success) {
            http_response_code(200); // OK
            echo json_encode(['message' => 'Comment updated successfully']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to update Comment']);
        }
    }

    /**
     * Delete a comment with the specified ID.
     */
    public function deleteComment(): void
    {
        $commentId = (int)Utility::splitURL()[3];

        $comment = Comment::getByID($commentId);

        if ($comment === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Comment not found']);
            return;
        }

        if ($comment->deleteComment()) {
            http_response_code(204); // No Content
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to delete comment']);
        }

    }
}
