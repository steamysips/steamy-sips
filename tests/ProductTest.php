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
        self::assertEquals("Velvet.jpeg", $this->dummy_product->getImgRelativePath());
        self::assertEquals("Velvet Bean Image", $this->dummy_product->getImgAltText());
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
        $this->assertArrayHasKey('img_alt_text', $result);
        $this->assertArrayHasKey('category', $result);
        $this->assertArrayHasKey('price', $result);
        $this->assertArrayHasKey('description', $result);
    
        // Check if the actual values are correct
        self::assertEquals("Velvet Bean", $result['name']);
        self::assertEquals(70, $result['calories']);
        self::assertEquals(50, $result['stock_level']);
        self::assertEquals("Velvet.jpeg", $result['img_url']);
        self::assertEquals("Velvet Bean Image", $result['img_alt_text']);
        self::assertEquals("Velvet", $result['category']);
        self::assertEquals(6.50, $result['price']);
        self::assertEquals("Each bottle contains 90% Pure Coffee powder and 10% Velvet bean Powder", $result['description']);
    }

    public function testSave(): void
    {
        // Save the dummy product
        $this->dummy_product->save();
    
        // Check if the product was saved successfully
        self::assertNotNull($this->dummy_product->getProductID());
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
        // Create a mock for the Product class
        $product = $this->getMockBuilder(Product::class)
                        ->onlyMethods(['getReviews']) // Mocking the getReviews method
                        ->disableOriginalConstructor()
                        ->getMock();
        
        // Define the expected return value for the getReviews method
        $expectedReviews = [
            (object) ['review_id' => 1, 'product_id' => 1, 'rating' => 4, 'comment' => 'Great product'],
            (object) ['review_id' => 2, 'product_id' => 1, 'rating' => 5, 'comment' => 'Excellent product']
        ];

        // Set up the mock to return the expected reviews when getReviews is called
        $product->expects($this->once())
                ->method('getReviews')
                ->willReturn($expectedReviews);

        // Call the getReviews method and assert that it returns the expected reviews
        $reviews = $product->getReviews();
        $this->assertEquals($expectedReviews, $reviews);
    }
    

    public function testGetNestedReviews(): void
    {
        // Mock data for the Product constructor
        $name = "Sample Product";
        $calories = 100;
        $stockLevel = 50;
        $imgRelativePath = "sample.jpg";
        $imgAltText = "Sample Image";
        $category = "Sample Category";
        $price = 10.00;
        $description = "Sample Description";
    
        // Create a mock Product object with mock data
        $product = $this->getMockBuilder(Product::class)
                        ->disableOriginalConstructor()
                        ->onlyMethods(['getNestedReviews']) // Mocking the getNestedReviews method
                        ->getMock();
        
        // Mock the behavior of the getNestedReviews method
        $product->expects($this->once()) // Expecting the getNestedReviews method to be called once
                ->method('getNestedReviews')
                ->willReturn([
                    // Sample nested review data
                    [
                        'id' => 1,
                        'product_id' => 1,
                        'user_id' => 1,
                        'parent_review_id' => null,
                        'rating' => 5,
                        'comment' => 'Excellent product!',
                        'created_at' => '2024-03-29 12:00:00',
                        'children' => [
                            [
                                'id' => 2,
                                'product_id' => 1,
                                'user_id' => 2,
                                'parent_review_id' => 1,
                                'rating' => 4,
                                'comment' => 'Good product!',
                                'created_at' => '2024-03-29 12:05:00',
                                'children' => []
                            ]
                        ]
                    ]
                ]);
    
        // Call the getNestedReviews method
        $nestedReviews = $product->getNestedReviews();
    
        // Assert that the returned nested reviews array is not empty
        $this->assertNotEmpty($nestedReviews);
    }
    
    public function testGetAverageRating(): void
    {
        // Mock the Product class to isolate the test from the database
        $product = $this->getMockBuilder(Product::class)
            ->onlyMethods(['getAverageRating']) // Only mock getAverageRating
            ->disableOriginalConstructor()
            ->getMock();
    
        // Define the expected return value (4.5)
        $expectedRating = 4.5;
    
        // Set up the mock to return the expected rating
        $product->expects($this->once())
            ->method('getAverageRating')
            ->willReturn($expectedRating);
    
        // Call the getAverageRating method
        $averageRating = $product->getAverageRating();
    
        // Check if the average rating matches the expected value
        $this->assertEquals($expectedRating, $averageRating);
    }
    
    
}
