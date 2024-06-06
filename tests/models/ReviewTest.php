<?php

declare(strict_types=1);

namespace Steamy\Tests\Model;

use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use Steamy\Model\Client;
use Steamy\Model\Location;
use Steamy\Model\Review;
use Steamy\Model\Product;
use Steamy\Tests\helpers\TestHelper;
use Steamy\Model\Comment;
use Throwable;

final class ReviewTest extends TestCase
{
    use TestHelper;

    private ?Review $dummy_review;
    private ?Client $reviewer;
    private ?Product $dummy_product;

    public static function setUpBeforeClass(): void
    {
        self::initFaker();
        self::resetDatabase();
    }

    public static function tearDownAfterClass(): void
    {
        self::$faker = null;
    }

    public function onNotSuccessfulTest(Throwable $t): never
    {
        self::printFakerSeed();
        parent::onNotSuccessfulTest($t);
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

        self::resetDatabase();
    }

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
            "Each bottle contains 90% Pure Coffee powder and 10% Velvet bean Powder"
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
     * Provides review data to testValidate and testSave
     *
     * @return array
     */
    public static function reviewDataProvider(): array
    {
        return [
            'valid review' => [
                'text' => 'Great product!',
                'rating' => 5,
                'created_date' => new DateTime('2023-01-01'),
                'expectedErrors' => []
            ],
            'too short text' => [
                'text' => 'A',
                'rating' => 3,
                'created_date' => new DateTime('2023-01-01'),
                'expectedErrors' => ['text' => 'Review text must have at least 2 characters']
            ],
            'too long text' => [
                'text' => str_repeat('A', 3000),
                'rating' => 3,
                'created_date' => new DateTime('2023-01-01'),
                'expectedErrors' => ['text' => 'Review text must have at most 2000 characters']
            ],
            'invalid rating' => [
                'text' => 'Good product',
                'rating' => 6,
                'created_date' => new DateTime('2023-01-01'),
                'expectedErrors' => ['rating' => 'Rating must be between 1 and 5']
            ],
            'invalid date' => [
                'text' => 'Good product',
                'rating' => 4,
                'created_date' => new DateTime('2030-01-01'),
                'expectedErrors' => ['date' => 'Review date cannot be in the future']
            ],
            'invalid date and rating' => [
                'text' => 'Good product',
                'rating' => -1,
                'created_date' => new DateTime('2030-01-01'),
                'expectedErrors' => [
                    'rating' => 'Rating must be between 1 and 5',
                    'date' => 'Review date cannot be in the future'
                ]
            ],
            'invalid text, date, and rating' => [
                'text' => '',
                'rating' => -1,
                'created_date' => new DateTime('2030-01-01'),
                'expectedErrors' => [
                    'text' => 'Review text must have at least 2 characters',
                    'rating' => 'Rating must be between 1 and 5',
                    'date' => 'Review date cannot be in the future'
                ]
            ]
        ];
    }

    /**
     * @dataProvider reviewDataProvider
     */
    public function testValidate(string $text, int $rating, DateTime $created_date, array $expectedErrors): void
    {
        $review = new Review(text: $text, rating: $rating, created_date: $created_date);
        $this->assertEquals($expectedErrors, $review->validate());
    }

    public function testGetByIDForValidId(): void
    {
        $fetched_review = Review::getByID($this->dummy_review->getReviewID());

        $this->assertNotNull($fetched_review);

        $expected_data = $this->dummy_review->toArray();
        $fetched_data = $fetched_review->toArray();

        // ignore creation dates because the date for expected review
        // was set by php while the date for fetched_data was set by mysql
        unset($expected_data['created_date']);
        unset($fetched_data['created_date']);

        // compare all attributes except created_date
        $this->assertEquals($expected_data, $fetched_data);

        // Compare dates by formatting
        $this->assertEquals(
            $this->dummy_review->getCreatedDate()->format('Y-m-d'),
            $fetched_review->getCreatedDate()->format('Y-m-d')
        );
    }

    public function testGetByIDForInvalidId(): void
    {
        // Test getByID with invalid ID
        $invalid_ids = [0, -1, 999, -111];
        foreach ($invalid_ids as $id) {
            $this->assertNull(Review::getByID($id));
        }
    }

    /**
     * @dataProvider reviewDataProvider
     */
    public function testSave(string $text, int $rating, DateTime $created_date, array $expectedErrors): void
    {
        $review = new Review(
            product_id: $this->dummy_product->getProductID(),
            client_id: $this->reviewer->getUserID(),
            text: $text,
            rating: $rating,
            created_date: $created_date
        );

        // Attempt to save the review
        $success = $review->save();

        // If expectedErrors array is empty, the review should be saved successfully
        $this->assertEquals(empty($expectedErrors), $success);
    }

    public function testGetNestedComments(): void
    {
        // Create a review
        $review = new Review(
            product_id: $this->dummy_product->getProductID(),
            client_id: $this->reviewer->getUserID(),
            text: "This is a test review for nested comments.",
            rating: 4
        );
        $review->save();

        // Create root level comment
        $comment1 = new Comment(
            review_id: $review->getReviewID(),
            user_id: $this->reviewer->getUserID(),
            text: "This is a root level comment."
        );
        $comment1->save();

        // Create nested comments
        $comment2 = new Comment(
            review_id: $review->getReviewID(),
            user_id: $this->reviewer->getUserID(),
            text: "This is a child comment.",
            parent_comment_id: $comment1->getCommentID()
        );
        $comment2->save();

        $comment3 = new Comment(
            review_id: $review->getReviewID(),
            user_id: $this->reviewer->getUserID(),
            text: "This is another root level comment."
        );
        $comment3->save();

        $comment4 = new Comment(
            review_id: $review->getReviewID(),
            user_id: $this->reviewer->getUserID(),
            text: "This is a child of a child comment.",
            parent_comment_id: $comment2->getCommentID()
        );
        $comment4->save();

        // Fetch nested comments
        $nestedComments = $review->getNestedComments();

        // Check if the structure is correct
        $this->assertIsArray($nestedComments);
        $this->assertCount(2, $nestedComments); // Should have 2 root level comments

        // Verify the first root level comment
        $this->assertEquals($comment1->getCommentID(), $nestedComments[0]->comment_id);
        $this->assertCount(1, $nestedComments[0]->children); // Should have 1 child

        // Verify the child comment of the first root level comment
        $this->assertEquals($comment2->getCommentID(), $nestedComments[0]->children[0]->comment_id);
        $this->assertCount(1, $nestedComments[0]->children[0]->children); // Should have 1 child

        // Verify the child of the child comment
        $this->assertEquals($comment4->getCommentID(), $nestedComments[0]->children[0]->children[0]->comment_id);
        $this->assertCount(0, $nestedComments[0]->children[0]->children[0]->children); // Should have no children

        // Verify the second root level comment
        $this->assertEquals($comment3->getCommentID(), $nestedComments[1]->comment_id);
        $this->assertCount(0, $nestedComments[1]->children); // Should have no children
    }


    /**
     * @throws Exception
     */
    public function testIsVerified(): void
    {
        // note: do not use data provider here because $faker is static and causes a bug
        $verified_review = self::createReview(self::createProduct(), self::createClient(), true);
        $unverified_review = self::createReview(self::createProduct(), self::createClient());
        $fake_review = new Review(review_id: -321, product_id: -32);

        $this->assertTrue($verified_review->isVerified());
        $this->assertFalse($unverified_review->isVerified());
        $this->assertFalse($fake_review->isVerified());
    }
}
