<?php

declare(strict_types=1);

namespace Steamy\Tests\Model;

use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use Steamy\Model\Product;
use Steamy\Model\Review;
use Steamy\Model\Client;
use Steamy\Tests\helpers\TestHelper;
use Throwable;


final class ProductTest extends TestCase
{
    use TestHelper;

    private ?Product $dummy_product;
    private ?Client $dummy_client;

    /**
     * @var Review|null A review written by $dummy_client for $dummy_product
     */
    private ?Review $dummy_review;


    public static function setUpBeforeClass(): void
    {
        self::initFaker();
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
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->dummy_client = self::createClient();

        // Create a dummy product for testing
        $this->dummy_product = self::createProduct();

        // Create a review object and save to the database
        $this->dummy_review = self::createReview($this->dummy_product, $this->dummy_client);
    }

    public function tearDown(): void
    {
        $this->dummy_product = null;
        $this->dummy_client = null;
        $this->dummy_review = null;

        self::resetDatabase();
    }

    public function testConstructor(): void
    {
        // Do not use dummy_product to test constructor as dummy_product attributes may change

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

        // Check if product attributes are correctly set
        self::assertEquals("Velvet Bean", $product->getName());
        self::assertEquals(70, $product->getCalories());
        self::assertEquals("Velvet.jpeg", $product->getImgRelativePath());
        self::assertEquals("Velvet Bean Image", $product->getImgAltText());
        self::assertEquals("Velvet", $product->getCategory());
        self::assertEquals(6.50, $product->getPrice());
        self::assertEquals(
            "Each bottle contains 90% Pure Coffee powder and 10% Velvet bean Powder",
            $product->getDescription()
        );
        self::assertInstanceOf(
            DateTime::class,
            $product->getCreatedDate()
        ); // Check if created_date is an instance of DateTime
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
        self::assertEquals($this->dummy_product->getName(), $result['name']);
        self::assertEquals($this->dummy_product->getCalories(), $result['calories']);
        self::assertEquals($this->dummy_product->getImgRelativePath(), $result['img_url']);
        self::assertEquals($this->dummy_product->getImgAltText(), $result['img_alt_text']);
        self::assertEquals($this->dummy_product->getCategory(), $result['category']);
        self::assertEquals($this->dummy_product->getPrice(), $result['price']);
        self::assertEquals(
            $this->dummy_product->getDescription(),
            $result['description']
        );
        self::assertInstanceOf(
            DateTime::class,
            $result['created_date']
        ); // Check if created_date is an instance of DateTime
    }

    public function testSave(): void
    {
        $this->markTestIncomplete(
            'Use data providers here for at least 3 test cases, ...',
        );
    }

    public function testValidate(): void
    {
        // Validate the dummy product
        $errors = $this->dummy_product->validate();

        // Check if there are no validation errors
        $this->assertEmpty($errors);

        $this->markTestIncomplete(
            'This test lacks test cases, ...',
        );
    }

    public function testGetRatingDistribution(): void
    {
        $distribution = $this->dummy_product->getRatingDistribution();

        // Check if the distribution contains the expected keys and values
        // Here dummy product contains a single review:
        $this->assertArrayHasKey($this->dummy_review->getRating(), $distribution);
        $this->assertEquals(100.0, $distribution[$this->dummy_review->getRating()]);

        $this->markTestIncomplete(
            'This test lacks test cases. This test might fail when getRatingDistribution excludes unverified reviews.',
        );
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

        $this->markTestIncomplete(
            'This test lacks test cases, ...',
        );
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

        $this->assertNotEquals(999.0, $averageRating);

        $this->markTestIncomplete(
            'This test lacks test cases, ...',
        );
    }

    public function testGetReviews(): void
    {
        $reviews = $this->dummy_product->getReviews();

        // Check if reviews are returned
        $this->assertNotEmpty($reviews);

        // Check if the reviews contain the expected values
        $this->assertCount(1, $reviews);
        $this->assertEquals($this->dummy_review->getText(), $reviews[0]->getText());
        $this->assertEquals($this->dummy_review->getRating(), $reviews[0]->getRating());
    }
}
