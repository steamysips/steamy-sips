<?php

namespace Steamy\Model;

use DateTime;
use Steamy\Core\Model; 
use Steamy\Core\Utility;
class Review
{
    use Model;
    private int $product_id;
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
        $this->product_id = $record->product_id;
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
                'product_id' => $this->product_id,
                'parent_review_id' => $this->parent_review_id,
                'text' => $this->text,
                'date' => $this->date,
                'rating' => $this->rating
            ];
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

    public function setProductID(int $productid): void
    {
        $this->product_id = $productid;
    }
    public function getParentReviewID(): int
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
    unset($reviewData['review_id']); // Remove review_id as it's auto-incremented

    // Perform insertion to the review table
    $this->insert($reviewData, 'review');
}

    public function validate(): array
    {
        $errors = [];

        if (empty($this->text)) {
            $errors['text'] = "Review text is required";
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
        // Query the database to check if the user with the given reviewId has purchased the product with the given productId
        $query = <<<EOL
        SELECT * FROM product 
        WHERE product_id = :product_id 
        AND user_id = (SELECT user_id FROM review WHERE review_id = :review_id)
        EOL;
    
        $result = self::get_row($query, ['product_id' => $product_id, 'review_id' => $review_id]);
    
        // If the result is greater than 0, the user has purchased the product
        return $result > 0;
    }
    

}