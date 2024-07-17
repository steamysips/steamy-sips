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

    protected string $table = 'review';
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

    /**
     * Retrieve all reviews from the database.
     *
     * @return array Array of Review objects representing all reviews in the database.
     */
    public static function getAll(): array
    {
        // Prepare and execute SQL query to retrieve all reviews
        $query = "SELECT * FROM review";
        $results = self::query($query);

        if (!$results) {
            return [];
        }

        // Fetch all reviews as Review objects
        $reviews = [];
        foreach ($results as $result) {
            $obj = new Review(
                product_id: $result->product_id,
                client_id: $result->client_id,
                text: $result->text,
                rating: $result->rating,
                created_date: Utility::stringToDate($result->created_date)
            );
            $obj->setReviewID($result->review_id);
            $reviews[] = $obj;
        }

        return $reviews;
    }

    /**
     * Retrieves all reviews for a particular product from the database.
     *
     * @param int $productId The ID of the product.
     * @return array An array containing all reviews for the specified product.
     */
    public static function getAllReviewsForProduct(int $productId): array
    {
        $query = "SELECT * FROM review WHERE product_id = :product_id";
        $params = ['product_id' => $productId];
        $result = self::query($query, $params);
        return $result ?: [];
    }

    /**
     * Updates review record in database but does not update the object itself.
     * @param array $newReviewData Associative array indexed by attribute name.
     * The values are the new review data.
     * @return bool Success or not
     */
    public function updateReview(array $newReviewData): bool
    {
        // remove review_id (if present) from user data
        unset($newReviewData['review_id']);

        return $this->update($newReviewData, ['review_id' => $this->review_id], $this->table);
    }

    public function deleteReview(): bool
    {
        return $this->delete($this->review_id, $this->table, 'review_id');
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
     * The review_id of the current object is updated after a successful insertion.
     * @return bool
     * @throws Exception
     */
    public function save(): bool
    {
        // If attributes of the object are invalid, exit
        $validation_errors = $this->validate();
        if (count($validation_errors) > 0) {
            throw new Exception(json_encode($validation_errors));
        }

        // Get data to be inserted into the review table
        $reviewData = $this->toArray();

        // let database handle  review_id and creation date
        unset($reviewData['review_id']);
        unset($reviewData['created_date']);

        // Perform insertion to the review table
        $inserted_id = $this->insert($reviewData, 'review');

        if ($inserted_id === null) {
            throw new Exception("Insertion failed for some unknown reason");
        }

        $this->review_id = $inserted_id;
        return true;
    }

    public function validate(): array
    {
        $errors = [];

        if (strlen($this->text) < 2) {
            $errors['text'] = "Review text must have at least 2 characters";
        }

        if (strlen($this->text) > 2000) {
            $errors['text'] = "Review text must have at most 2000 characters";
        }

        if (!filter_var($this->rating, FILTER_VALIDATE_INT, [
            "options" => [
                "min_range" => Review::MIN_RATING,
                "max_range" => Review::MAX_RATING
            ]
        ])) {
            $errors['rating'] = sprintf(
                "Rating must be between %d and %d",
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
     * Check if the review author has purchased the product.
     *
     * @return bool True if the writer has purchased the product, false otherwise.
     */
    public function isVerified(): bool
    {
        // Count the number of times the review author has purchased the product
        $query = <<<EOL
        SELECT COUNT(*) as purchase_count
        FROM order_product op
        JOIN `order` o ON op.order_id = o.order_id
        JOIN review r ON r.client_id = o.client_id
        WHERE r.review_id = :review_id 
        AND op.product_id = :product_id
        EOL;

        $result = self::get_row($query, ['product_id' => $this->product_id, 'review_id' => $this->review_id]);

        if ($result === false) {
            return false;
        }

        return $result->purchase_count > 0;
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

        // Order the children array of each comment by created_date
        foreach ($commentMap as $comment) {
            usort($comment->children, function ($a, $b) {
                return strtotime($a->created_date) - strtotime($b->created_date);
            });
        }

        // Filter out comments that have a parent (i.e., retain only root-level comments)
        $nestedComments = array_filter($commentMap, function ($review) {
            return $review->parent_comment_id === null;
        });

        // Reset the keys of the array to maintain continuity
        return array_values($nestedComments);
    }
}
