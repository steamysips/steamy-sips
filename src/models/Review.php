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

    private int $product_id;
    private int $user_id;
    private ?int $parent_review_id; // a top-level review does not have a parent
    private int $review_id;
    private string $text;
    private int $rating;
    private Datetime $date;

    public function __construct(
        int $user_id,
        int $product_id,
        ?int $parent_review_id,
        string $text,
        int $rating,
        DateTime $date
    ) {
        $this->review_id = -1;
        $this->user_id = $user_id;
        $this->product_id = $product_id;
        $this->parent_review_id = $parent_review_id;
        $this->text = htmlspecialchars_decode(strip_tags($text));
        $this->rating = $rating;
        $this->date = $date;
    }


    /**
     * @throws Exception
     */
    public static function getAll(): array
    {
        $query = "SELECT * FROM review";
        $results = self::query($query);

        // convert results to an array of Review
        $reviews = [];
        foreach ($results as $result) {
            // convert date to DateTime object
            $date_obj = null;
            try {
                $date_obj = new DateTime($result->date);
            } catch (Exception $e) {
                error_log('Error converting date: ' . $e->getMessage());
            }

            $obj = new Review(
                $result->user_id,
                $result->product_id,
                $result->parent_review_id,
                $result->text,
                $result->rating,
                $date_obj,
            );

            $obj->setReviewID($result->review_id);
            $reviews[] = $obj;
        }
        return $reviews;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'review_id' => $this->review_id,
            'product_id' => $this->product_id,
            'parent_review_id' => $this->parent_review_id,
            'text' => $this->text,
            'date' => $this->date,
            'rating' => $this->rating
        ];
    }
    

    /**
     * Retrieves a review by its ID.
     *
     * @param int $id The ID of the review to retrieve.
     * @return Review|null The review object if found, otherwise null.
     * @throws Exception If an error occurs during the database query.
     */
    public static function getByID(int $id): ?Review
    {
        $query = "SELECT * FROM review WHERE review_id = :id";
        $params = ['id' => $id];

        try {
            $result = Review::query($query, $params); // Execute the query
            if (!empty($result)) {
                // Create a new Review object using the retrieved data
                $review = new Review(
                    $result[0]->user_id,
                    $result[0]->product_id,
                    $result[0]->parent_review_id,
                    $result[0]->text,
                    $result[0]->rating,
                    $result[0]->date
                );
                $review->setReviewID($id); // Set the review ID
                return $review;
            }
        } catch (Exception $e) {
            throw new Exception("Error fetching review: " . $e->getMessage());
        }

        return null; // Return null if review not found
    }

    public function getReviewID(): int
    {
        return $this->review_id;
    }

    public function setReviewID(int $review_id): void
    {
        $this->review_id = $review_id;
    }

    public function getUserID(): int
    {
        return $this->user_id;
    }

    public function setUserID(int $user_id): void
    {
        $this->review_id = $user_id;
    }

    public function getProductID(): int
    {
        return $this->product_id;
    }

    public function setProductID(int $productID): void
    {
        $this->product_id = $productID;
    }

    public function getParentReviewID(): ?int
    {
        return $this->parent_review_id;
    }


    public function setParentReviewID(int $parent_review_id): void
    {
        $this->review_id = $parent_review_id;
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

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    public function save(): void
    {
        // If attributes of the object are invalid, exit
        if (count($this->validate()) > 0) {
            Utility::show($this->validate());
            return;
        }
        // Get data to be inserted into the review table
        $reviewData = $this->toArray();

        // Remove review_id as it is auto-incremented in database
        unset($reviewData['review_id']);

        // Perform insertion to the review table
        $this->insert($reviewData, 'review');
    }

    public function validate(): array
    {
        $errors = [];
        if (strlen($this->text) < 2) {
            $errors['text'] = "Review text must have at least 2 characters";
        }
        if ($this->rating < 1 || $this->rating > 5) {
            $errors['rating'] = "Rating must be between 1 and 5";
        }
        if ($this->date > new DateTime()) {
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
        JOIN review r ON r.user_id = o.user_id
        WHERE r.review_id = :review_id 
        AND op.product_id = :product_id
        EOL;

        $result = self::get_row($query, ['product_id' => $product_id, 'review_id' => $review_id]);

        // If result is empty, the user has written the review for the product
        return empty($result);
    }

}