<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Model\Product;
use Steamy\Model\Review; // Import Review class if not already imported

final class ProductTest extends TestCase
{
    private ?Product $dummy_product;

    public function setUp(): void
    {
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

}
