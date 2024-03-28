<?php

declare(strict_types=1);

namespace Steamy\Model;

use Exception;
use Steamy\Core\Model;
use Steamy\Core\Utility;

class Product
{
    use Model;

    protected string $table = 'product';
    private int $product_id;
    private string $name;
    private int $calories;
    private int $stock_level;
    private string $img_url;
    private string $img_alt_text;
    private string $category;
    private float $price;
    private string $description;

    public function __construct(
        string $name,
        int $calories,
        int $stock_level,
        string $img_url,
        string $img_alt_text,
        string $category,
        float $price,
        string $description
    ) {
        $this->product_id = -1; // product_id of a new product is determined by database
        $this->name = $name;
        $this->calories = $calories;
        $this->stock_level = $stock_level;
        $this->img_url = $img_url;
        $this->img_alt_text = $img_alt_text;
        $this->category = $category;
        $this->price = $price;
        $this->description = $description;
    }

    public static function getByID(int $product_id): false|Product
    {
        $query = "SELECT * FROM product where product_id = :product_id";
        $record = self::get_row($query, ['product_id' => $product_id]);

        if (!$record) {
            return false;
        }

        $product_obj = new Product(
            $record->name,
            $record->calories,
            $record->stock_level,
            $record->img_url,
            $record->img_alt_text,
            $record->category,
            (float)$record->price,
            $record->description
        );

        $product_obj->setProductID($record->product_id);
        return $product_obj;
    }

    public static function getCategories(): array
    {
        $query = "SELECT category FROM product";
        $result = self::query($query);

        if (empty($result)) {
            return [];
        }

        $callback = fn($obj): string => $obj->category;

        return array_map($callback, $result);
    }

    public function toArray(): array
    {
        return
            [
                'product_id' => $this->product_id,
                'name' => $this->name,
                'calories' => $this->calories,
                'stock_level' => $this->stock_level,
                'img_url' => $this->img_url,
                'img_alt_text' => $this->img_alt_text,
                'category' => $this->category,
                'price' => $this->price,
                'description' => $this->description
            ];
    }

    /**
     * @return array An array of Product objects
     */
    public static function getAll(): array
    {
        $query = "SELECT * FROM product";
        $results = self::query($query);

        // convert results to an array of Product
        $products = [];
        foreach ($results as $result) {
            $obj = new Product(
                $result->name,
                $result->calories,
                $result->stock_level,
                $result->img_url,
                $result->img_alt_text,
                $result->category,
                (float)$result->price,
                $result->description
            );
            $obj->setProductID($result->product_id);
            $products[] = $obj;
        }
        return $products;
    }

    public function getProductID(): int
    {
        return $this->product_id;
    }

    public function setProductID(int $product_id): void
    {
        $this->product_id = $product_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCalories(): int
    {
        return $this->calories;
    }

    public function setCalories(int $calories): void
    {
        $this->calories = $calories;
    }

    public function getStockLevel(): int
    {
        return $this->stock_level;
    }

    public function setStockLevel(int $stock_level): void
    {
        $this->stock_level = $stock_level;
    }

    /**
     * @return string Absolute URL of image
     */
    public function getImgAbsolutePath(): string
    {
        return ROOT . "/assets/img/product/" . $this->img_url;
    }

    /**
     * @return string Path to image relative to image folder
     */
    public function getImgRelativePath(): string
    {
        return $this->img_url;
    }

    public function setImgUrl(string $img_url): void
    {
        $this->img_url = $img_url;
    }

    public function getImgAltText(): string
    {
        return $this->img_alt_text;
    }

    public function setImgAltText(string $img_alt_text): void
    {
        $this->img_alt_text = $img_alt_text;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function save(): void
    {
        // If attributes of the object are invalid, exit
        if (count($this->validate()) > 0) {
            Utility::show($this->validate());
            return;
        }

        // Get data to be inserted into the product table
        $productData = $this->toArray();
        unset($productData['product_id']); // Remove product_id as it's auto-incremented

        // Perform insertion to the product table
        $this->insert($productData, 'product');
    }

    public function getAverageRating(): float
    {
    // Query the database to calculate the average rating
    $query = "SELECT AVG(rating) AS average_rating
              FROM review
              WHERE product_id = :product_id AND parent_review_id IS NULL"; // Exclude child reviews
    $params = ['product_id' => $this->product_id];

    try {
        $result = $this->query($query, $params);
    } catch (Exception $e) {
        error_log('Error fetching average rating: ' . $e->getMessage());
        return 0; // Return 0 if there's an error fetching the rating
    }

    // Extract the average rating from the result array
    if (!empty($result)) {
        $averageRating = $result[0]->average_rating;
        return $averageRating !== null ? round($averageRating, 2) : 0; // Round to two decimal places
    }

    return 0; // No reviews, return 0 as the average rating
    }
     


    public function validate(): array
    {
        $errors = [];

        // Validate name
        if (empty($this->name)) {
            $errors['name'] = "Product name is required";
        }

        // Validate calories
        if ($this->calories < 0) {
            $errors['calories'] = "Calories must be non-negative";
        }

        // Validate stock level
        if ($this->stock_level < 0) {
            $errors['stock_level'] = "Stock level must be non-negative";
        }

        // Validate img_url
        if (!preg_match('/\.(png|jpeg|avif)$/', $this->img_url)) {
            $errors['img_url'] = "Image URL must end with .png, .jpeg, or .avif";
        }

        // Validate img_alt_text
        $altTextLength = strlen($this->img_alt_text);
        if ($altTextLength < 5 || $altTextLength > 150) {
            $errors['img_alt_text'] = "Image alternative text must be between 5 and 150 characters";
        }

        // Validate category
        if (strlen($this->category) === 0) {
            $errors['category'] = "Category must have length greater than 0";
        }

        // Validate price
        if ($this->price <= 0) {
            $errors['price'] = "Price must be greater than 0";
        }

        // Validate description
        if (strlen($this->description) === 0) {
            $errors['description'] = "Description must have length greater than 0";
        }

        return $errors;
    }


    /**
     * Returns all reviews for product
     *
     * @return Review[] An array of Review objects
     * @throws Exception
     */
    public function getReviews(): array
    {
        // Initialize an empty array to store review objects
        $reviews = [];

        // Query the database for reviews related to this product
        $query = "SELECT * FROM review WHERE product_id = :product_id";
        $params = ['product_id' => $this->product_id];

        try {
            $reviewRecords = $this->query($query, $params);
        } catch (Exception $e) {
            error_log('Error fetching reviews: ' . $e->getMessage());
            return $reviews;
        }

        // Iterate through the retrieved review records and create Review objects
        foreach ($reviewRecords as $record) {
            // Create a new Review object and add it to the reviews array
            $review = new Review($record->review_id);
            $reviews[] = $review;
        }

        return $reviews;
    }

    /**
     * Returns an array of reviews where each review has a
     * `children` attribute but no `parent_review_id` attribute.
     * The `children` attribute contains an arrays of reviews who are children
     * of the current review.
     *
     * @return array An array of reviews in nested format
     */
    public function getNestedReviews(): array
    {
        // Fetch all reviews for the given product ID
        $reviews = $this->query(
            "SELECT * FROM review WHERE product_id = :product_id",
            ['product_id' => $this->product_id]
        );

        // Create an associative array to store reviews by their review_id
        $reviewMap = [];
        foreach ($reviews as $review) {
            $reviewMap[$review->review_id] = $review;
            $reviewMap[$review->review_id]->children = [];
        }

        // Populate the children array for each review based on parent_review_id
        foreach ($reviews as $review) {
            if ($review->parent_review_id !== null) {
                // Add the review as a child to its parent review
                $reviewMap[$review->parent_review_id]->children[] = $reviewMap[$review->review_id];
            }
        }

        // Filter out reviews that have a parent (i.e., retain only root-level reviews)
        $nestedReviews = array_filter($reviewMap, function ($review) {
            return $review->parent_review_id === null;
        });

        // Reset the keys of the array to maintain continuity
        return array_values($nestedReviews);
    }
}
