<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Model\Product;
use Steamy\Model\Review;
use Steamy\Core\Database;
use Steamy\Model\Location;
use Steamy\Model\Client;


final class ProductTest extends TestCase
{
    use Database;
    private ?Product $dummy_product;
    private ?Client $dummy_client;
    private ?Review $dummy_review;

    public function setUp(): void
    {
        $address = new Location("Royal Road", "Curepipe", 1);
        $this->dummy_client = new Client(
            "jo@gmail.com",
            "john",
            "johnny",
            "abcd",
            "13213431",
            $address
        );

        $success = $this->dummy_client->save();
        if (!$success) {
            throw new Exception('Unable to save client');
        }

        // Create a dummy product for testing
        $this->dummy_product = new Product(
            "Velvet Bean",
            70,
            "Velvet.jpeg",
            "Velvet Bean Image",
            "Velvet",
            6.50,
            "Each bottle contains 90% Pure Coffee powder and 10% Velvet bean Powder",
            new DateTime()
        );

        $success = $this->dummy_product->save();
        if (!$success) {
            throw new Exception('Unable to save product');
        }

        // Create a review object and save to the database
        $this->dummy_review = new Review(
            product_id: $this->dummy_product->getProductID(),
            client_id: $this->dummy_client->getUserID(),
            text: "This is a test review.",
            rating: 5,
            created_date: new DateTime()
        );
        $success = $this->dummy_review->save();

        if (!$success) {
            throw new Exception('Unable to save review');
        }
    }

    public function tearDown(): void
    {
        $this->dummy_product = null;
        $this->dummy_client = null;
        $this->dummy_review = null;

        // Clear all data from product, review, and client tables
        self::query('DELETE FROM review; DELETE FROM product; DELETE FROM client;');
    }

    public function testConstructor(): void
    {
        // Check if product attributes are correctly set
        self::assertEquals("Velvet Bean", $this->dummy_product->getName());
        self::assertEquals(70, $this->dummy_product->getCalories());
        self::assertEquals("Velvet.jpeg", $this->dummy_product->getImgRelativePath());
        self::assertEquals("Velvet Bean Image", $this->dummy_product->getImgAltText());
        self::assertEquals("Velvet", $this->dummy_product->getCategory());
        self::assertEquals(6.50, $this->dummy_product->getPrice());
        self::assertEquals("Each bottle contains 90% Pure Coffee powder and 10% Velvet bean Powder", $this->dummy_product->getDescription());
        self::assertInstanceOf(DateTime::class, $this->dummy_product->getCreatedDate()); // Check if created_date is an instance of DateTime
    }

    public function testToArray(): void
    {
        $result = $this->dummy_product->toArray();

        // Check if all required keys are present
        $this->assertArrayHasKey('product_id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('calories', $result);
        $this->assertArrayHasKey('img_url', $result);
        $this->assertArrayHasKey('img_alt_text', $result);
        $this->assertArrayHasKey('category', $result);
        $this->assertArrayHasKey('price', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('created_date', $result); // Ensure created_date is included in toArray result

        // Check if the actual values are correct
        self::assertEquals("Velvet Bean", $result['name']);
        self::assertEquals(70, $result['calories']);
        self::assertEquals("Velvet.jpeg", $result['img_url']);
        self::assertEquals("Velvet Bean Image", $result['img_alt_text']);
        self::assertEquals("Velvet", $result['category']);
        self::assertEquals(6.50, $result['price']);
        self::assertEquals("Each bottle contains 90% Pure Coffee powder and 10% Velvet bean Powder", $result['description']);
        self::assertInstanceOf(DateTime::class, $result['created_date']); // Check if created_date is an instance of DateTime
    }

    public function testSave(): void
    {
        // Save the dummy product
        $result = $this->dummy_product->save();

        // Check if the product was saved successfully
        self::assertTrue($result); // Assert that save() returns true upon successful save
        self::assertNotNull($this->dummy_product->getProductID());
    }

    public function testValidate(): void
    {
        // Validate the dummy product
        $errors = $this->dummy_product->validate();

        // Check if there are no validation errors
        $this->assertEmpty($errors);
    }

    public function testGetRatingDistribution(): void
    {
        $distribution = $this->dummy_product->getRatingDistribution();

        // Check if the distribution contains the expected keys and values
        $this->assertArrayHasKey(5, $distribution);
        $this->assertEquals(100.0, $distribution[5]); // 1 out of 1 reviews is 5 stars
    }

    public function testDeleteProduct(): void
    {
        $product_id = $this->dummy_product->getProductID();
        $result = $this->dummy_product->deleteProduct();

        // Check if the product was deleted successfully
        $this->assertTrue($result);

        // Check if the product no longer exists in the database
        $product = Product::getByID($product_id);
        $this->assertNull($product);
    }

    public function testUpdateProduct(): void
    {
        $newData = [
            'name' => 'Updated Velvet Bean',
            'calories' => 75,
            'img_url' => 'UpdatedVelvet.jpeg',
            'img_alt_text' => 'Updated Velvet Bean Image',
            'category' => 'Updated Velvet',
            'price' => 7.00,
            'description' => 'Updated description'
        ];

        $result = $this->dummy_product->updateProduct($newData);

        // Check if the product was updated successfully
        $this->assertTrue($result);

        // Reload the product from the database and check the updated values
        $updatedProduct = Product::getByID($this->dummy_product->getProductID());
        $this->assertEquals('Updated Velvet Bean', $updatedProduct->getName());
        $this->assertEquals(75, $updatedProduct->getCalories());
        $this->assertEquals('UpdatedVelvet.jpeg', $updatedProduct->getImgRelativePath());
        $this->assertEquals('Updated Velvet Bean Image', $updatedProduct->getImgAltText());
        $this->assertEquals('Updated Velvet', $updatedProduct->getCategory());
        $this->assertEquals(7.00, $updatedProduct->getPrice());
        $this->assertEquals('Updated description', $updatedProduct->getDescription());
    }

    public function testGetAverageRating(): void
    {
        $averageRating = $this->dummy_product->getAverageRating();

        // Check if the average rating is correct
        $this->assertEquals(5.0, $averageRating);
    }

    public function testGetReviews(): void
    {
        $reviews = $this->dummy_product->getReviews();

        // Check if reviews are returned
        $this->assertNotEmpty($reviews);

        // Check if the reviews contain the expected values
        $this->assertCount(1, $reviews);
        $this->assertEquals('This is a test review.', $reviews[0]->getText());
        $this->assertEquals(5, $reviews[0]->getRating());
    }
}
