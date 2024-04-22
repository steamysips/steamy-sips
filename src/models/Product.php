<?php

declare(strict_types=1);

namespace Steamy\Model;

use DateTime;
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
    private string $img_url;
    private string $img_alt_text;
    private string $category;
    private float $price;
    private string $description;
    private DateTime $created_date;

    public function __construct(
        string $name,
        int $calories,
        string $img_url,
        string $img_alt_text,
        string $category,
        float $price,
        string $description,
        ?DateTime $created_date = new DateTime()
    ) {
        $this->product_id = -1; // product_id of a new product is determined by database
        $this->name = $name;
        $this->calories = $calories;
        $this->img_url = $img_url;
        $this->img_alt_text = $img_alt_text;
        $this->category = $category;
        $this->price = $price;
        $this->description = $description;
        $this->created_date = $created_date;
    }

    public static function getByID(int $product_id): ?Product
    {
        if ($product_id < 0) {
            return null;
        }

        $query = "SELECT * FROM product where product_id = :product_id";
        $record = self::get_row($query, ['product_id' => $product_id]);

        if (!$record) {
            return null;
        }

        $product_obj = new Product(
            name: $record->name,
            calories: $record->calories,
            img_url: $record->img_url,
            img_alt_text: $record->img_alt_text,
            category: $record->category,
            price: (float)$record->price,
            description: $record->description,
            created_date: Utility::stringToDate($record->created_date)
        );

        $product_obj->setProductID($record->product_id);
        return $product_obj;
    }

    /**
     * @return string[] A list of product categories
     */
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
                'img_url' => $this->img_url,
                'img_alt_text' => $this->img_alt_text,
                'category' => $this->category,
                'price' => $this->price,
                'description' => $this->description,
                'created_date' => $this->created_date
            ];
    }

    /**
     * @return Product[] An array of Product objects
     */
    public static function getAll(): array
    {
        $query = "SELECT * FROM product";
        $results = self::query($query);

        // convert results to an array of Product
        $products = [];
        foreach ($results as $result) {
            $obj = new Product(
                name: $result->name,
                calories: $result->calories,
                img_url: $result->img_url,
                img_alt_text: $result->img_alt_text,
                category: $result->category,
                price: (float)$result->price,
                description: $result->description,
                created_date: $result->created_date
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

    public function setCreatedDate(DateTime $new_date): void
    {
        $this->created_date = $new_date;
    }

    public function getCreatedDate(): DateTime
    {
        return $this->created_date;
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

    public function save(): bool
    {
        // If attributes of the object are invalid, exit
        if (count($this->validate()) > 0) {
            return false;
        }

        // Get data to be inserted into the product table
        $productData = $this->toArray();
        unset($productData['product_id']); // Remove product_id as it's auto-incremented

        unset($productData['created_date']); // let database add this field

        // Perform insertion to the product table
        try {
            $this->insert($productData, 'product');
        } catch (Exception) {
            return false;
        }
        return true;
    }

    public function getAverageRating(): float
    {
        // Ensure that $product_id is initialized
        if (!isset($this->product_id)) {
            return 0; // Return 0 if $product_id is not set
        }

        // Query the database to calculate the average rating
        // TODO: Exclude unverified reviews
        $query = <<< EOL
           SELECT AVG(rating) AS average_rating
           FROM review
           WHERE product_id = :product_id
           EOL;
        $params = ['product_id' => $this->product_id];

        $result = $this->query($query, $params);

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
     * Returns all reviews for product.
     *
     * @return Review[] An array of Review objects
     */
    public function getReviews(bool $orderByDate = true): array
    {
        // Initialize an empty array to store review objects
        $reviews = [];

        // Query the database for reviews related to this product
        $query = "SELECT * FROM review WHERE product_id = :product_id";

        if ($orderByDate) {
            $query .= ' ORDER BY date;';
        }

        $params = ['product_id' => $this->product_id];
        $reviewRecords = $this->query($query, $params);

        if (empty($reviewRecords)) {
            return [];
        }

        // Iterate through the retrieved review records and create Review objects
        foreach ($reviewRecords as $result) {
            // Create a new Review object and add it to the reviews array
            $review = new Review(
                review_id: $result->review_id,
                product_id: $result->product_id,
                client_id: $result->client_id,
                text: $result->text,
                rating: $result->rating,
                created_date: Utility::stringToDate($result->created_date),
            );
            $reviews[] = $review;
        }
        return $reviews;
    }


    /**
     * Returns an associative array containing the distribution of ratings for the product.
     * The key is the rating value (1 to 5) and the value is the percentage of reviews with that rating.
     *
     * @return array An associative array representing the rating distribution
     */
    public function getRatingDistribution(): array
    {
        // Query the database to get the percentage distribution of ratings
        $query = <<< EOL
            SELECT rating, 
            COUNT(*) * 100.0 / (SELECT COUNT(*) FROM review WHERE product_id = :product_id) AS percentage
            FROM review
            WHERE product_id = :product_id
            GROUP BY rating
        EOL;

        $params = ['product_id' => $this->product_id];
        $result = $this->query($query, $params);

        if (empty($result)) {
            return [];
        }

        // Initialize the distribution array
        $distribution = [];

        // Populate the distribution array with rating and percentage
        foreach ($result as $row) {
            $rating = $row->rating;
            $percentage = round((float)$row->percentage, 1); // Round to 1 decimal place
            $distribution[$rating] = $percentage;
        }

        return $distribution;
    }

}