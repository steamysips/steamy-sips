<?php

declare(strict_types=1);

namespace Steamy\Tests\Model;

use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use Steamy\Core\Database;
use Steamy\Model\Client;
use Steamy\Model\Location;
use Steamy\Model\Product;
use Steamy\Model\Review;

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
            "john_u@gmail.com", "john", "johhny", "User0",
            "13213431", new Location("Royal Road", "Curepipe", 1)
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
            'DELETE FROM comment; DELETE FROM review; DELETE FROM client; DELETE FROM user; DELETE FROM store_product; DELETE FROM product;'
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

    public function testValidate(): void
    {
        // Test validation with valid data
        $errors = $this->dummy_review->validate();
        $this->assertEmpty($errors);
        // Test validation with empty text
        $invalidReview = new Review(
            1, 1, 1, "", 0, new DateTime("2024-03-10")
        );
        $errors = $invalidReview->validate();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('text', $errors);
        $this->assertEquals('Review text must have at least 2 characters', $errors['text']); // Assert specific message
        // Test validation with invalid rating
        $invalidReview = new Review(
            1, 1, 1, "Valid Text", -1, new DateTime("2024-03-10")
        );
        $errors = $invalidReview->validate();
        $this->assertNotEmpty($errors);
        $this::assertArrayHasKey('rating', $errors);
        $this->assertEquals('Rating must be between 1 and 5', $errors['rating']); // Assert specific message
    }

    public function testGetById(): void
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
        // Create a DateTime object for the review date
        $date = new DateTime('now');
        // Create a mock Review object with mock data
        $review = $this->getMockBuilder(Review::class)
            ->setConstructorArgs([1, 1, 1, 'Test review', 5, $date])
            ->onlyMethods(['save']) // Mocking the save method
            ->getMock();
        // Set up the mock to expect the save method to be called once
        $review->expects($this->once())
            ->method('save');
        // Call the save method
        $review->save();
    }
}