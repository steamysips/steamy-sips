<?php

declare(strict_types=1);

namespace Steamy\Model;

use DateTime;
use Exception;
use Steamy\Core\Model;
use Steamy\Core\Utility;

class Review
{
    use Model;

    private int $review_id;
    private int $product_id;

    /**
     * ID of client who wrote the review
     * @var int
     */
    private int $client_id;
    private string $text;
    private int $rating;
    private Datetime $created_date;
    public const MAX_RATING = 5;
    public const MIN_RATING = 1;

    public function __construct(
        ?int $review_id = null,
        ?int $product_id = null,
        ?int $client_id = null,
        ?string $text = '',
        ?int $rating = null,
        ?DateTime $created_date = null
    ) {
        $this->review_id = $review_id ?? -1;
        $this->product_id = $product_id ?? -1;
        $this->client_id = $client_id ?? -1;
        $this->text = $text ?? '';
        $this->rating = $rating ?? 0;
        $this->created_date = $created_date ?? new DateTime();
    }

    public function toArray(): array
    {
        return [
            'review_id' => $this->review_id,
            'product_id' => $this->product_id,
            'client_id' => $this->client_id,
            'text' => $this->text,
            'rating' => $this->rating,
            'created_date' => $this->created_date->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Retrieves a review by its ID.
     *
     * @param int $review_id The ID of the review to retrieve.
     * @return Review|null The review object if found, otherwise null.
     */
    public static function getByID(int $review_id): ?Review
    {
        if (empty($review_id) || $review_id < 0) {
            return null;
        }

        $query = "SELECT * FROM review WHERE review_id = :id";
        $params = ['id' => $review_id];

        $result = Review::query($query, $params);

        if (empty($result)) {
            return null;
        }

        $result = $result[0];

        return new Review(
            review_id: $result->review_id,
            product_id: $result->product_id,
            client_id: $result->client_id,
            text: $result->text,
            rating: $result->rating,
            created_date: Utility::stringToDate($result->created_date)
        );
    }

    public function getReviewID(): int
    {
        return $this->review_id;
    }

    public function setReviewID(int $review_id): void
    {
        $this->review_id = $review_id;
    }

    public function getClientID(): int
    {
        return $this->client_id;
    }

    public function setClientID(int $client_id): void
    {
        $this->review_id = $client_id;
    }

    public function getProductID(): int
    {
        return $this->product_id;
    }

    public function setProductID(int $productID): void
    {
        $this->product_id = $productID;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    public function getCreatedDate(): DateTime
    {
        return $this->created_date;
    }

    public function setCreatedDate(DateTime $created_date): void
    {
        $this->created_date = $created_date;
    }

    /**
     * Saves review to database if attributes are valid. review_id and created_date attributes
     * are automatically set by database and any set values are ignored.
     * @return bool
     */
    public function save(): bool
    {
        // If attributes of the object are invalid, exit
        if (count($this->validate()) > 0) {
            return false;
        }

        // Get data to be inserted into the review table
        $reviewData = $this->toArray();

        // Remove review_id as it is auto-incremented in database
        unset($reviewData['review_id']);

        unset($reviewData['created_date']); // let database handle creation date

        // Perform insertion to the review table
        try {
            $this->insert($reviewData, 'review');
            return true;
        } catch (Exception) {
            return false;
        }
    }

    public function validate(): array
    {
        $errors = [];

        if (strlen($this->text) < 2) {
            $errors['text'] = "Review text must have at least 2 characters";
        }

        if (!filter_var($this->rating, FILTER_VALIDATE_INT, [
            "options" => [
                "min_range" => Review::MIN_RATING,
                "max_range" => Review::MAX_RATING
            ]
        ])) {
            $errors['rating'] = sprintf(
                "Review must be between %d and %d",
                Review::MIN_RATING,
                Review::MAX_RATING
            );
        }

        if ($this->created_date > new DateTime()) {
            $errors['date'] = "Review date cannot be in the future";
        }

        return $errors;
    }

    /**
     * Check if the writer of the review has purchased the product.
     *
     * @param int $product_id The ID of the product to check.
     * @param int $review_id The ID of the review.
     * @return bool True if the writer has purchased the product, false otherwise.
     */
    public static function isVerified(int $product_id, int $review_id): bool
    {
        // Query the database to check if the review with the given review_id belongs to the user who wrote it
        $query = <<<EOL
        SELECT COUNT(*) 
        FROM order_product op
        JOIN `order` o ON op.order_id = o.order_id
        JOIN review r ON r.client_id = o.client_id
        WHERE r.review_id = :review_id 
        AND op.product_id = :product_id
        EOL;

        $result = self::get_row($query, ['product_id' => $product_id, 'review_id' => $review_id]);

        // If result is empty, the user has written the review for the product
        return empty($result);
    }

    /**
     * Returns an array of comments where each comment has a
     * `children` attribute but no `parent_comment_id` attribute.
     * The `children` attribute stores an arrays of comments
     * which are children of the current comment.
     *
     * @return array An array of comments in nested format
     */
    public function getNestedComments(): array
    {
        $comments = $this->query(
            "SELECT * FROM comment WHERE review_id = :review_id",
            ['review_id' => $this->review_id]
        );

        if (empty($comments)) {
            return [];
        }

        // Create an associative array to store comments by their comment_id
        $commentMap = [];
        foreach ($comments as $comment) {
            $commentMap[$comment->comment_id] = $comment;
            $commentMap[$comment->comment_id]->children = [];
        }

        // Populate the children array for each comment based on parent_comment_id
        foreach ($comments as $comment) {
            // If a comment has a parent
            if ($comment->parent_comment_id !== null) {
                // Add the comment as a child of its parent comment
                $commentMap[$comment->parent_comment_id]->children[] = $commentMap[$comment->comment_id];
            }
        }

        // Filter out comments that have a parent (i.e., retain only root-level comments)
        $nestedComments = array_filter($commentMap, function ($review) {
            return $review->parent_comment_id === null;
        });

        // Reset the keys of the array to maintain continuity
        return array_values($nestedComments);
    }
}