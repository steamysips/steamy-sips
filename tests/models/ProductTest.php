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
        self::resetDatabase();
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
        // Prepare test data
        $newProductData = [
            'name' => 'New Product',
            'calories' => 100,
            'img_url' => 'new_product.jpeg',
            'img_alt_text' => 'New Product Image',
            'category' => 'New Category',
            'price' => 10.00,
            'description' => 'New Product Description'
        ];

        // Create a new product object with the test data
        $newProduct = new Product(
            $newProductData['name'],
            $newProductData['calories'],
            $newProductData['img_url'],
            $newProductData['img_alt_text'],
            $newProductData['category'],
            $newProductData['price'],
            $newProductData['description']
        );

        // Save the product to the database
        $result = $newProduct->save();

        // Assert that the product was saved successfully
        $this->assertTrue($result);

        // Fetch the saved product from the database
        $savedProduct = Product::getByID($newProduct->getProductID());

        // Assert that the saved product matches the test data
        $this->assertEquals($newProductData['name'], $savedProduct->getName());
        $this->assertEquals($newProductData['calories'], $savedProduct->getCalories());
        $this->assertEquals($newProductData['img_url'], $savedProduct->getImgRelativePath());
        $this->assertEquals($newProductData['img_alt_text'], $savedProduct->getImgAltText());
        $this->assertEquals($newProductData['category'], $savedProduct->getCategory());
        $this->assertEquals($newProductData['price'], $savedProduct->getPrice());
        $this->assertEquals($newProductData['description'], $savedProduct->getDescription());
    }


    public function testValidate(): void
    {
        // Prepare test data with invalid values
        $invalidProductData = [
            'name' => '', // Empty name
            'calories' => -10, // Negative calories
            'img_url' => 'invalid_image.txt', // Invalid image extension
            'img_alt_text' => 'In', // Invalid image alt text length
            'category' => '', // Empty category
            'price' => 0, // Zero price
            'description' => '' // Empty description
        ];

        // Create a new product object with the invalid test data
        $invalidProduct = new Product(
            $invalidProductData['name'],
            $invalidProductData['calories'],
            $invalidProductData['img_url'],
            $invalidProductData['img_alt_text'],
            $invalidProductData['category'],
            $invalidProductData['price'],
            $invalidProductData['description']
        );

        // Validate the product
        $errors = $invalidProduct->validate();

        // Assert that validation errors are present for each invalid field
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('calories', $errors);
        $this->assertArrayHasKey('img_url', $errors);
        $this->assertArrayHasKey('img_alt_text', $errors);
        $this->assertArrayHasKey('category', $errors);
        $this->assertArrayHasKey('price', $errors);
        $this->assertArrayHasKey('description', $errors);

        // Assert that there are exactly 7 validation errors
        $this->assertCount(7, $errors);
    }


    /**
     * @throws Exception
     */
    public function testGetRatingDistribution(): void
    {
        // reset data from setUp
        self::resetDatabase();

        // Create a new product for testing
        $product = self::createProduct();
        $this->dummy_client = self::createClient();

        // Create mock review data with different ratings
        $verifiedReviewRatings = [5, 4, 3, 2, 1, 5, 4, 3, 4, 5];
        // Insert mock review data into the database
        foreach ($verifiedReviewRatings as $reviewData) {
            self::createReview($product, $this->dummy_client, $reviewData, true);
        }

        // Create a random number of unverified reviews with different ratings
        for ($i = 0; $i < self::$faker->numberBetween(0, 10); $i++) {
            $rating = self::$faker->numberBetween(1, 5);
            self::createReview($product, self::createClient(), $rating);
        }

        // Retrieve the rating distribution for the product
        $ratingDistribution = $product->getRatingDistribution();

        // Assert that the rating distribution is accurate
        $expectedDistribution = [
            1 => 10.0, // 1 star
            2 => 10.0, // 2 stars
            3 => 20.0, // 3 stars
            4 => 30.0, // 4 stars
            5 => 30.0, // 5 stars
        ];
        $this->assertEquals($expectedDistribution, $ratingDistribution);
    }

    public function testDeleteProduct(): void
    {
        // Save the product to the database
        $product = $this->dummy_product;

        // Delete the product from the database
        $success = $product->deleteProduct();
        // Assert that the delete operation was successful
        self::assertTrue($success);
        // Try to retrieve the product by ID to check if it was deleted
        $deletedProduct = Product::getByID($product->getProductID());
        // Assert that the product is no longer in the database
        self::assertNull($deletedProduct);
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

    /**
     * @throws Exception
     */
    public function testGetAverageRating(): void
    {
        // reset database as we don't want previously created reviews from setUp.
        self::resetDatabase();

        $this->dummy_product = self::createProduct();
        $this->dummy_client = self::createClient();

        // Create a random number of verified reviews with different ratings
        $verifiedReviewRatings = [];
        for ($i = 0; $i < self::$faker->numberBetween(0, 10); $i++) {
            $rating = self::$faker->numberBetween(1, 5);
            $verifiedReviewRatings[] = $rating;
            self::createReview($this->dummy_product, $this->dummy_client, $rating, true);
        }

        // Note:  $this->dummy_client can be a verified reviewer do not write unverified reviews with it

        // Create a random number of unverified reviews with different ratings
        for ($i = 0; $i < self::$faker->numberBetween(0, 10); $i++) {
            $rating = self::$faker->numberBetween(1, 5);
            self::createReview($this->dummy_product, self::createClient(), $rating);
        }

        // Retrieve the average rating for the product
        $averageRating = $this->dummy_product->getAverageRating();

        // Assert that the average rating is accurate
        $expectedAverageRating = count($verifiedReviewRatings) === 0 ? 0 : (float)array_sum(
                $verifiedReviewRatings
            ) / count($verifiedReviewRatings);
        $this->assertEqualsWithDelta($expectedAverageRating, $averageRating, 0.0001);
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
