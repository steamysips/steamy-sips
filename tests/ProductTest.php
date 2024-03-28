<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Model\Product;

final class ProductTest extends TestCase
{
    private ?Product $dummy_product;

    public function setUp(): void
    {
        // Create a dummy product for testing
        $this->dummy_product = new Product("Velvet Bean", 70, 50, "Velvet.jpeg", "Velvet Bean Image", "Velvet", 6.50, "Each bottle contains 90% Pure Coffee powder and 10% Velvet bean Powder");
    }

    public function tearDown(): void
    {
        $this->dummy_product = null;
    }

    public function testConstructor(): void
    {
        // Check if product attributes are correctly set
        self::assertEquals("Velvet Bean", $this->dummy_product->getName());
        self::assertEquals(70, $this->dummy_product->getCalories());
        self::assertEquals(50, $this->dummy_product->getStockLevel());
        self::assertEquals("Velvet.jpeg", $this->dummy_product->getImgUrl());
        self::assertEquals("Velvet Bean Image", $this->dummy_product->getImageAltText());
        self::assertEquals("Velvet", $this->dummy_product->getCategory());
        self::assertEquals(6.50, $this->dummy_product->getPrice());
        self::assertEquals("Each bottle contains 90% Pure Coffee powder and 10% Velvet bean Powder", $this->dummy_product->getDescription());
    }

    public function testToArray(): void
    {
        $result = $this->dummy_product->toArray();

        // Check if all required keys are present
        $this->assertArrayHasKey('product_id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('calories', $result);
        $this->assertArrayHasKey('stock_level', $result);
        $this->assertArrayHasKey('img_url', $result);
        $this->assertArrayHasKey('image_alt_text', $result);
        $this->assertArrayHasKey('category', $result);
        $this->assertArrayHasKey('price', $result);
        $this->assertArrayHasKey('description', $result);

        $expectedResult = [
            'product_id' => null, // As product_id is auto-incremented, it will be null for a new product
            'name' => 'Velvet Bean',
            'calories' => 70,
            'stock_level' => 50,
            'img_url' => 'Velvet.jpeg',
            'image_alt_text' => 'Velvet Bean Image',
            'category' => 'Velvet',
            'price' => 6.50,
            'description' => 'Each bottle contains 90% Pure Coffee powder and 10% Velvet bean Powder'
        ];
    
        // Get the result of toArray() method
        $result = $this->dummy_product->toArray();
    
        // Check if the result matches the expected array
        $this->assertEquals($expectedResult, $result);
    }

    public function testSave(): void
    {
        // Save the dummy product
        $this->dummy_product->save();

        // Check if the product was saved successfully
        self::assertGreaterThan(0, $this->dummy_product->getProductID());
    }

    public function testValidate(): void
    {
        // Validate the dummy product
        $errors = $this->dummy_product->validate();

        // Check if there are no validation errors
        $this->assertEmpty($errors);
    }

    public function testGetReviews(): void
    {
        // Returns an array of reviews
        $reviews = $this->dummy_product->getReviews();

        // Check if the returned reviews array is not empty
        $this->assertIsArray($reviews);
        $this->assertNotEmpty($reviews);
    }

    public function testGetNestedReviews(): void
    {
        // Returns an array of nested reviews
        $nestedReviews = $this->dummy_product->getNestedReviews();

        // Check if the returned nested reviews array is not empty
        $this->assertIsArray($nestedReviews);
        $this->assertNotEmpty($nestedReviews);
    }
}
