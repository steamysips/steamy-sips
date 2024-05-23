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
        self::query('DELETE FROM comment; DELETE FROM review; DELETE FROM client; DELETE FROM user; DELETE FROM store_product; DELETE FROM product;');
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
    // Test with valid data
    $validReview = new Review(1, 1, 1, "Valid review text", 5, new DateTime("2024-03-10"));
    $validErrors = $validReview->validate();
    $this->assertEmpty($validErrors);

    // Test with invalid text
    $invalidTextReview = new Review(1, 1, 1, "", 5, new DateTime("2024-03-10"));
    $textErrors = $invalidTextReview->validate();
    $this->assertArrayHasKey('text', $textErrors);

    // Test with invalid rating
    $invalidRatingReview = new Review(1, 1, 1, "Invalid rating review", 6, new DateTime("2024-03-10"));
    $ratingErrors = $invalidRatingReview->validate();
    $this->assertArrayHasKey('rating', $ratingErrors);

    // Test with future date
    $futureDateReview = new Review(1, 1, 1, "Future date review", 5, new DateTime("2030-01-01"));
    $dateErrors = $futureDateReview->validate();
    $this->assertArrayHasKey('date', $dateErrors);
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
    }

        public function testGetNestedComments(): void
    {
        // Create a mock review with review_id 1
        $mockReview = new Review(
            1,
            $this->dummy_product->getProductID(),
            $this->reviewer->getUserID(),
            "Test review",
            5,
            new DateTime("2024-03-10")
        );

        // Create mock comments
        $comment1 = (object) [
            'comment_id' => 1,
            'review_id' => 1,
            'parent_comment_id' => null,
            'text' => 'Comment 1',
            'created_date' => '2024-03-10 10:00:00'
        ];

        $comment2 = (object) [
            'comment_id' => 2,
            'review_id' => 1,
            'parent_comment_id' => 1,
            'text' => 'Reply to Comment 1',
            'created_date' => '2024-03-10 10:01:00'
        ];

        // Mock the query method of Review class to return the mock comments
        $mockReview->query = function ($query, $params) use ($comment1, $comment2) {
            return ($params['review_id'] == 1) ? [$comment1, $comment2] : [];
        };

        // Call the getNestedComments method
        $nestedComments = $mockReview->getNestedComments();

        // Assert that the nested comments are properly structured
        $this->assertCount(1, $nestedComments); // Only one root-level comment
        $this->assertEquals($comment1->text, $nestedComments[0]->text); // Check text of the root comment
        $this->assertCount(1, $nestedComments[0]->children); // One child comment under the root comment
        $this->assertEquals($comment2->text, $nestedComments[0]->children[0]->text); // Check text of the child comment
    }

    public function testIsVerified(): void
    {
        // Mock the get_row method of Review class to return a result indicating verified purchase
        $mockReview = $this->getMockBuilder(Review::class)
            ->onlyMethods(['get_row'])
            ->getMock();

        $mockReview->method('get_row')->willReturn((object)['purchase_count' => 1]);

        // Set product_id and review_id for the mock review
        $mockReview->setProductID($this->dummy_product->getProductID());
        $mockReview->setReviewID($this->dummy_review->getReviewID());

        // Call the isVerified method
        $isVerified = $mockReview->isVerified();

        // Assert that the review is verified
        $this->assertTrue($isVerified);
    }
}