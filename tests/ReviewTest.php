<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Core\Database;
use Steamy\Model\Client;
use Steamy\Model\Location;
use Steamy\Model\Order;
use Steamy\Model\OrderProduct;
use Steamy\Model\Review;
use Steamy\Model\Product;
use Steamy\Model\Store;
use Faker\Factory;
use Faker\Generator;

final class ReviewTest extends TestCase
{
    use Database;

    private static ?Generator $faker;
    private ?Review $dummy_review;
    private ?Client $reviewer;
    private ?Product $dummy_product;

    public static function setUpBeforeClass(): void
    {
        self::$faker = Factory::create();
    }

    public static function tearDownAfterClass(): void
    {
        self::$faker = null;
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


        self::query(
            "DELETE FROM order_product;
                    DELETE FROM `order`;
                    DELETE FROM comment;
                    DELETE FROM review;
                    DELETE FROM client;
                    DELETE FROM user;
                    DELETE FROM store_product;
                    DELETE FROM store;
                    DELETE FROM product;
                    "
        );
    }

    /**
     * Creates a client and saves it to database
     * @return Client
     * @throws Exception
     */
    public static function createClient(): Client
    {
        $client = new Client(
            self::$faker->email(),
            self::$faker->name(),
            self::$faker->name(),
            "User0",
            "13213431",
            new Location("Royal Road", "Curepipe", 1)
        );

        $success = $client->save();
        if (!$success) {
            throw new Exception('Unable to save client');
        }
        return $client;
    }

    /**
     * Creates a product and saves it to database.
     * @return Product
     * @throws Exception
     */
    public static function createProduct(): Product
    {
        $product = new Product(
            "Velvet Bean",
            70,
            "Velvet.jpeg",
            "Velvet Bean Image",
            "Velvet",
            6.50,
            "Each bottle contains 90% Pure Coffee powder and 10% Velvet bean Powder",
            new DateTime()
        );

        $success = $product->save();
        if (!$success) {
            throw new Exception('Unable to save product');
        }
        return $product;
    }

    /**
     * Create a review and saves it to database.
     * @param Product $product A valid product already present in database
     * @param Client $client A valid client already present in database
     * @param bool $verified Whether to create an order for client for given product.
     * @return Review
     * @throws Exception
     */
    public static function createReview(Product $product, Client $client, bool $verified = false): Review
    {
        if ($verified) {
            // place an order for  client and product

            // create store
            $store = new Store(
                phone_no: "13213431",
                address: new Location(
                    street: "Royal",
                    city: "Curepipe",
                    district_id: 1,
                    latitude: 50,
                    longitude: 50
                )
            );
            $success = $store->save();
            if (!$success) {
                throw new Exception('Unable to create store');
            }

            // Add stock to the store for the product to be bought
            $store->addProductStock($product->getProductID(), 10);

            $order = new Order($store->getStoreID(), $client->getUserID(), [
                new OrderProduct($product->getProductID(), 'small', 'oat', 1)
            ]);

            $success = $order->save();
            if (!$success) {
                throw new Exception('Unable to save order');
            }
        }

        $review = new Review(
            product_id: $product->getProductID(),
            client_id: $client->getUserID(),
            text: "This is a test review.",
            rating: 5
        );

        $success = $review->save();

        if (!$success) {
            throw new Exception('Unable to save review');
        }

        return $review;
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

    public function testGetByID(): void
    {
        $fetched_review = Review::getByID($this->dummy_review->getReviewID());

        $this->assertNotNull($fetched_review);

        // Assert that the properties of the returned Review object match the data
        $this->assertEquals($this->dummy_review->getText(), $fetched_review->getText());
        $this->assertEquals($this->dummy_review->getRating(), $fetched_review->getRating());

        // Compare dates by formatting
        $this->assertEquals(
            $this->dummy_review->getCreatedDate()->format('Y-m-d'),
            $fetched_review->getCreatedDate()->format('Y-m-d')
        );

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
        $this->markTestIncomplete(
            'This test lacks test cases, ...',
        );

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
