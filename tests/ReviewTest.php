<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Core\Database;
use Steamy\Model\Client;
use Steamy\Model\Location;
use Steamy\Model\Review;
use Steamy\Model\Product;

final class ReviewTest extends TestCase
{
    use Database;

    private ?Review $dummy_review;
    private ?Client $reviewer;
    private ?Product $dummy_product;


    /**
     * Adds a client and a review to the database.
     *
     * Note: All tables in test database except product and district tables are initially empty.
     * @throws Exception
     */
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

        $success = $this->dummy_product->save();
        if (!$success) {
            throw new Exception('Unable to save product');
        }

        // create a client object and save to database
        $this->reviewer = new Client(
            "john_u@gmail.com",
            "john",
            "johhny",
            "User0",
            "13213431",
            new Location("Royal Road", "Curepipe", 1)
        );

        $success = $this->reviewer->save();
        if (!$success) {
            throw new Exception('Unable to save client');
        }

        // create a review object and save to database
        $this->dummy_review = new Review(
            1,
            $this->dummy_product->getProductID(),
            $this->reviewer->getUserID(),
            "This is a test review.",
            5
        );
        $success = $this->dummy_review->save();

        if (!$success) {
            throw new Exception('Unable to save client');
        }
    }

    /**
     * Clears previously inserted data in database.
     * @return void
     */
    public function tearDown(): void
    {
        $this->dummy_review = null;
        $this->reviewer = null;
        $this->dummy_product = null;

        // clear all data from review and client tables
        self::query(
            'DELETE FROM comment; DELETE FROM review; DELETE FROM client;
                DELETE FROM user; DELETE FROM store_product; DELETE FROM product;'
        );
    }

    public function testConstructor(): void
    {
        $new_review = new Review(
            1,
            1,
            1,
            "This is a test review.",
            5,
            new DateTime("2024-03-10"),
        );

        // Check if review attributes are correctly set
        self::assertEquals(1, $new_review->getClientID());
        self::assertEquals(1, $new_review->getProductID());
        self::assertEquals("This is a test review.", $new_review->getText());
        self::assertEquals(5, $new_review->getRating());
        self::assertEquals(new DateTime("2024-03-10"), $new_review->getCreatedDate());


        // Check default values of constructor
        $new_review = new Review();
        self::assertEquals(-1, $new_review->getClientID());
        self::assertEquals(-1, $new_review->getProductID());
        self::assertEquals("", $new_review->getText());
        self::assertEquals(0, $new_review->getRating());
    }

    public function testToArray(): void
    {
        $result = $this->dummy_review->toArray();

        // Check if all required keys are present
        $this->assertArrayHasKey('client_id', $result);
        $this->assertArrayHasKey('product_id', $result);
        $this->assertArrayHasKey('text', $result);
        $this->assertArrayHasKey('created_date', $result);
        $this->assertArrayHasKey('rating', $result);

        // Check if the actual values are correct
        self::assertEquals($this->reviewer->getUserID(), $result['client_id']);
        self::assertEquals("This is a test review.", $result['text']);
        self::assertEquals(
            $this->dummy_review->getCreatedDate()->format('Y-m-d H:i:s'),
            $result['created_date']
        ); // Compare dates
        self::assertEquals(5, $result['rating']);
    }

    /**
     * Data provider for testValidate.
     *
     * @return array
     */
    public static function validateDataProvider(): array
    {
        return [
            'valid review' => [
                'text' => 'Great product!',
                'rating' => 5,
                'created_date' => new DateTime('2023-01-01'),
                'expectedErrors' => []
            ],
            'short text' => [
                'text' => 'A',
                'rating' => 3,
                'created_date' => new DateTime('2023-01-01'),
                'expectedErrors' => ['text' => 'Review text must have at least 2 characters']
            ],
            'invalid rating' => [
                'text' => 'Good product',
                'rating' => 6,
                'created_date' => new DateTime('2023-01-01'),
                'expectedErrors' => ['rating' => 'Rating must be between 1 and 5']
            ],
            'future date' => [
                'text' => 'Good product',
                'rating' => 4,
                'created_date' => new DateTime('2030-01-01'),
                'expectedErrors' => ['date' => 'Review date cannot be in the future']
            ]
        ];
    }

    /**
     * @dataProvider validateDataProvider
     */
    public function testValidate(string $text, int $rating, DateTime $created_date, array $expectedErrors): void
    {
        $review = new Review(text: $text, rating: $rating, created_date: $created_date);
        $this->assertEquals($expectedErrors, $review->validate());
    }

    public function testGetByID(): void
    {
        $fetched_review = Review::getByID($this->dummy_review->getReviewID());

        $this->assertNotNull($fetched_review);

        // Assert that the properties of the returned Review object match the mock data
        self::assertEquals($this->dummy_review->getText(), $fetched_review->getText());
        self::assertEquals($this->dummy_review->getRating(), $fetched_review->getRating());

        // Compare dates by formatting
        self::assertEquals(
            $this->dummy_review->getCreatedDate()->format('Y-m-d'),
            $fetched_review->getCreatedDate()->format('Y-m-d')
        );

        // Test getByID with invalid ID
        $this->assertNull(Review::getByID(999));
    }

    public function testSave(): void
    {
        // Create an invalid review with empty text
        $invalidReview = new Review(1, 1, 1, "", 0, new DateTime("2024-03-10"));
        // Attempt to save the invalid review
        $success = $invalidReview->save();
        // Assert that the save operation failed
        $this->assertFalse($success);

        // Create a valid review
        $validReview = new Review(
            product_id: $this->dummy_product->getProductID(),
            client_id: $this->reviewer->getUserID(),
            text: "Another test review",
            rating: 4,
            created_date: new DateTime()
        );
        // Attempt to save the valid review
        $success = $validReview->save();
        // Assert that the save operation succeeded
        $this->assertTrue($success);
        $this->assertGreaterThan(0, $validReview->getReviewID());

        // Create a review with special characters
        $specialCharReview = new Review(
            product_id: $this->dummy_product->getProductID(),
            client_id: $this->reviewer->getUserID(),
            text: "Review with special characters: !@#$%^&*()",
            rating: 4,
            created_date: new DateTime()
        );
        // Attempt to save the review with special characters
        $success = $specialCharReview->save();
        // Assert that the save operation succeeded
        $this->assertTrue($success);
        $this->assertGreaterThan(0, $specialCharReview->getReviewID());


        // Create a review of length exactly 2000
        $longTextReview = new Review(
            product_id: $this->dummy_product->getProductID(),
            client_id: $this->reviewer->getUserID(),
            text: str_repeat("A", 2000),
            rating: 4,
            created_date: new DateTime()
        );
        // Attempt to save the review with long text
        $success = $longTextReview->save();
        // Assert that the save operation failed because max length of review is 2000
        $this->assertTrue($success);

        // Create a review with extremely long text
        $longTextReview = new Review(
            product_id: $this->dummy_product->getProductID(),
            client_id: $this->reviewer->getUserID(),
            text: str_repeat("A", 10000),
            rating: 4,
            created_date: new DateTime()
        );
        // Attempt to save the review with long text
        $success = $longTextReview->save();
        // Assert that the save operation failed because max length of review is 2000
        $this->assertFalse($success);


        // Test saving duplicate reviews
        $duplicateReview = new Review(
            product_id: $this->dummy_product->getProductID(),
            client_id: $this->reviewer->getUserID(),
            text: "This is a test review.",
            rating: 5,
            created_date: new DateTime()
        );
        // Attempt to save the duplicate review
        $success = $duplicateReview->save();
        // Assert that the save operation succeeded (assuming duplicates are allowed)
        $this->assertTrue($success);
        $this->assertGreaterThan(0, $duplicateReview->getReviewID());
    }

    public function testGetNestedComments(): void
    {
        $review = new Review(review_id: 1);
        $comments = $review->getNestedComments();

        $this->assertIsArray($comments);
        foreach ($comments as $comment) {
            $this->assertObjectHasAttribute('children', $comment);
            if (!empty($comment->children)) {
                foreach ($comment->children as $childComment) {
                    $this->assertObjectHasAttribute('children', $childComment);
                }
            }
        }
    }

    public function testIsVerified(): void
    {
        $review = new Review(review_id: 2, product_id: 2);
        $this->assertFalse($review->isVerified());
    }
}
