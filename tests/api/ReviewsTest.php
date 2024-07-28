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

    public function tearDown(): void
    {
        self::resetDatabase();
    }

    public function testCreateReview()
    {
        self::markTestIncomplete('TODO');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testUpdateReview()
    {
        $review = self::createReview(self::createProduct(), self::createClient(), 4);

        // Create new review data
        $newData = [
            'text' => 'Updated review text',
            'rating' => 4,
        ];

        // Send PUT request to update the review
        $response = self::$guzzle->put('reviews/' . $review->getReviewID(), [
            'json' => $newData,
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $body = $response->getBody();
        $json = json_decode($body->getContents(), true);

        // Check if the update was successful
        $this->assertEquals('Review updated successfully', $json['message']);

        // Fetch the review from the database
        $saved_review = Review::getByID($review->getReviewID());

        // Verify the review was updated
        $this->assertEquals($newData['text'], $saved_review->getText());
        $this->assertEquals($newData['rating'], $saved_review->getRating());

        // ensure that all other attributes did not change
        assertEquals($review->getReviewID(), $saved_review->getReviewID());
        assertEquals($review->getProductID(), $saved_review->getProductID());
        assertEquals($review->getClientID(), $saved_review->getClientID());

        $this->assertEquals(
            $review->getCreatedDate()->format('Y-m-d'),
            $saved_review->getCreatedDate()->format('Y-m-d')
        );
    }

    public function testDeleteReview()
    {
        self::markTestIncomplete('TODO');
    }
}