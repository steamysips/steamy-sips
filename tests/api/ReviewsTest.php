<?php

declare(strict_types=1);

namespace Steamy\Tests\Api;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Steamy\Model\Review;
use Steamy\Tests\helpers\APIHelper;
use Steamy\Tests\helpers\TestHelper;
use Throwable;

use function PHPUnit\Framework\assertEquals;

final class ReviewsTest extends TestCase
{
    use TestHelper;
    use APIHelper;

    private Review $dummy_review;

    public static function setUpBeforeClass(): void
    {
        self::initFaker();
        self::initGuzzle();
        self::resetDatabase();
    }

    public static function tearDownAfterClass(): void
    {
        self::$faker = null;
        self::$guzzle = null;
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
        $this->dummy_review = self::createReview();
    }

    public function tearDown(): void
    {
        self::resetDatabase();
    }

    /**
     * @throws GuzzleException
     */
    public function testUpdateReview()
    {
        // Create new review data
        $newData = [
            'text' => 'Updated review text',
            'rating' => 4,
            'client_id' => $this->dummy_review->getClientID(),
            'product_id' => $this->dummy_review->getProductID(),
        ];

        // Send PUT request to update the review
        $response = self::$guzzle->put('reviews/' . $this->dummy_review->getReviewID(), [
            'json' => $newData,
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $body = $response->getBody();
        $json = json_decode($body->getContents(), true);

        // Check if the update was successful
        $this->assertEquals('Review updated successfully', $json['message']);

        // Fetch the updated review
        $response = self::$guzzle->get('reviews/' . $this->dummy_review->getReviewID());
        $this->assertEquals(200, $response->getStatusCode());

        $updatedReview = json_decode($response->getBody()->getContents(), true);

        // Verify the review was updated
        $this->assertEquals($newData['text'], $updatedReview['text']);
        $this->assertEquals($newData['rating'], $updatedReview['rating']);
    }

    // Helper function to create a review for testing
    private static function createReview(): Review
    {
        $review = new Review(
            null,
            self::$faker->randomDigitNotNull,
            self::$faker->randomDigitNotNull,
            self::$faker->text(100),
            self::$faker->numberBetween(1, 5)
        );
        $review->save();
        return $review;
    }
}