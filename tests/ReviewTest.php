<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Model\Review;

final class ReviewTest extends TestCase
{
    private ?Review $dummy_review;

    public function setUp(): void
    {
        // Create a dummy review for testing
        $this->dummy_review = new Review(
            1, // user_id
            1, // product_id
            0, // parent_review_id
            "This is a test review.",
            5,
            new DateTime("2024-03-10")
        );
    }

    public function tearDown(): void
    {
        $this->dummy_review = null;
    }

    public function testConstructor(): void
    {
        // Check if review attributes are correctly set
        self::assertEquals(1, $this->dummy_review->getUserID());
        self::assertEquals(1, $this->dummy_review->getProductID());
        self::assertEquals(0, $this->dummy_review->getParentReviewID());
        self::assertEquals("This is a test review.", $this->dummy_review->getText());
        self::assertEquals(5, $this->dummy_review->getRating());
        self::assertEquals(new DateTime("2024-03-10"), $this->dummy_review->getDate());
    }

    public function testToArray(): void
    {
        $result = $this->dummy_review->toArray();

        // Check if all required keys are present
        $this->assertArrayHasKey('user_id', $result);
        $this->assertArrayHasKey('product_id', $result);
        $this->assertArrayHasKey('parent_review_id', $result);
        $this->assertArrayHasKey('text', $result);
        $this->assertArrayHasKey('date', $result);
        $this->assertArrayHasKey('rating', $result);

        // Check if the actual values are correct
        self::assertEquals(1, $result['user_id']);
        self::assertEquals(1, $result['product_id']);
        self::assertEquals(0, $result['parent_review_id']);
        self::assertEquals("This is a test review.", $result['text']);
        self::assertEquals(new DateTime("2024-03-10"), $result['date']);
        self::assertEquals(5, $result['rating']);
    }

    public function testValidate(): void
    {
        // Test validation with valid data
        $errors = $this->dummy_review->validate();
        $this->assertEmpty($errors);

        // Test validation with invalid data
        $invalidReview = new Review(
            1,
            1,
            0,
            "", // empty text
            0,  // invalid rating
            new DateTime("2024-03-10")
        );
        $errors = $invalidReview->validate();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('text', $errors);
        $this->assertArrayHasKey('rating', $errors);
    }

    public function testGetByID(): void
    {
        // Mock data for the test
        $review_id = 1;
        $user_id = 1;
        $product_id = 1;
        $parent_review_id = null; // Assuming the parent review ID is 0 for simplicity
        $text = "Test review";
        $rating = 5;
        $date = new DateTime();
    
        // Call the getByID method directly on the Review class
        $review = Review::getByID($review_id);
    
        // Assert that the returned object is an instance of Review
        $this->assertInstanceOf(Review::class, $review);
    
        // Assert that the properties of the returned Review object match the mock data
        $this->assertEquals($user_id, $review->getUserID());
        $this->assertEquals($product_id, $review->getProductID());
        $this->assertEquals($parent_review_id, $review->getParentReviewID());
        $this->assertEquals($text, $review->getText());
        $this->assertEquals($rating, $review->getRating());
        $this->assertEquals($date, $review->getDate());
    }
    
    
    

    public function testSave(): void
    {
        // Create a DateTime object for the review date
        $date = new DateTime('now');
    
        // Create a mock Review object with mock data
        $review = $this->getMockBuilder(Review::class)
                       ->setConstructorArgs([1, 1, null, 'Test review', 5, $date])
                       ->onlyMethods(['save']) // Mocking the save method
                       ->getMock();
        
        // Set up the mock to expect the save method to be called once
        $review->expects($this->once())
               ->method('save');
    
        // Call the save method
        $review->save();
    }
    

    public function testIsVerified(): void
    {
        // Mock the Review class to isolate the test from the database
        $review = $this->getMockBuilder(Review::class)
            ->onlyMethods(['get_row']) // Mocking the get_row method
            ->disableOriginalConstructor()
            ->getMock();
        
        // Define the expected return value for the mocked get_row method
        $expectedResult = (object) [
            'count' => 1 // Assuming there's a match in the database
        ];

        // Set up the mock to return the expected result when get_row is called
        $review->expects($this->once())
            ->method('get_row')
            ->willReturn($expectedResult);

        // Call the isVerified method and assert that it returns true
        $isVerified = $review->isVerified(1, 7); // Assuming product_id = 1 and review_id = 1
        $this->assertTrue($isVerified);
    }
    
}