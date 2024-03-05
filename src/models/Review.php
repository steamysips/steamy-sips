<?php

namespace Steamy\Model;

use DateTime;
use Steamy\Core\Model; 

class review
{
    use Model;
    private int $productid;
    private int $user_id;
    private int $parent_review_id;
    private int $review_id;
    private string $text;
    private int $rating;
    private Datetime $date;

    public function __construct(int $id)
    {
        // Fetch data from the database based on the ID

        $record = $this->first(
            (array)[
                'review_id' => $id,
            ]
        );

        $this->review_id = $record->review_id;
        $this->user_id = $record->user_id;
        $this->productid = $record->product_id;
        $this->parent_review_id = $record->parent_review_id ?: null;
        $this->text = htmlspecialchars_decode(strip_tags($record->text));
        $this->rating = $record->rating;
        $this->date = new DateTime($record->date);
    }

    public function toArray(): array
    {
        return
            (array)[
                'user_id' => $this->user_id,
                'review_id' => $this->review_id,
                'product_id' => $this->productid,
                'parent_review_id' => $this->parent_review_id,
                'text' => $this->text,
                'date' => $this->date,
                'rating' => $this->rating
            ];
    }

    public function getReviewId(): int
    {
        return $this->review_id;
    }
    public function setReviewId(int $review_id): void
    {
        $this->review_id = $review_id;
    }
    public function getUserId(): int
    {
        return $this->user_id;
    }
    public function setUserId(int $user_id): void
    {
        $this->review_id = $user_id;
    }
    public function getProductId(): int
    {
        return $this->productid;
    }

    public function setProductId(int $productid): void
    {
        $this->productid = $productid;
    }
    public function getParentReviewId(): int
    {
        return $this->parent_review_id;
    }
    public function setParentReviewId(int $parent_review_id): void
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

    public function validate(): array
    {
        $errors = [];

        if (empty($this->text)) {
            $errors['text'] = "Review text is required";
        }

        if (empty($this->productid)) {
            $errors['product_id'] = "Product ID is required";
        }

        if (empty($this->user_id)) {
            $errors['user_id'] = "User ID is required";
        }

        if (empty($text)) {
            $errors['text'] = "Text can not be empty";
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
     * @param int $productId The ID of the product to check.
     * @param int $reviewId The ID of the review.
     * @return bool True if the writer has purchased the product, false otherwise.
     */
    public static function isVerified(int $productId, int $reviewId): bool
    {
        // Query the database to check if the user with the given reviewId has purchased the product with the given productId
        $query = <<<EOL
        SELECT COUNT(*) FROM purchases 
        WHERE product_id = :productId 
        AND user_id = (SELECT user_id FROM reviews WHERE review_id = :reviewId)
        EOL;
    
        $result = self::get_row($query, ['productId' => $productId, 'reviewId' => $reviewId]);
    
        // If the result is greater than 0, the user has purchased the product
        if ($result > 0) {
            return true;
        } else {
            return false;
        }
    }
    

}