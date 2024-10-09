<?php

declare(strict_types=1);

namespace Steamy\Tests\Api;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Steamy\Model\Comment;
use Steamy\Model\Review;
use Steamy\Model\Product;
use Steamy\Model\Client;
use Steamy\Tests\helpers\APIHelper;
use Steamy\Tests\helpers\TestHelper;
use Throwable;

use function PHPUnit\Framework\assertEquals;

final class CommentsTest extends TestCase
{
    use TestHelper;
    use APIHelper;

    private Comment $dummy_comment;
    private Review $dummy_review;
    private Product $dummy_product;
    private Client $dummy_client;

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
        $this->dummy_client = self::createClient();
        $this->dummy_product = self::createProduct();
        $this->dummy_review = self::createReview($this->dummy_product, $this->dummy_client);
        $this->dummy_comment = new Comment(
            user_id: $this->dummy_client->getUserID(),
            review_id: $this->dummy_review->getReviewID(),
            text: self::$faker->sentence()
        );
        $this->dummy_comment->save();
    }

    public function tearDown(): void
    {
        self::resetDatabase();
    }

    /**
     * @throws GuzzleException
     */
    public function testGetAllComments()
    {
        $response = self::$guzzle->get('comments');
        $this->assertEquals(200, $response->getStatusCode());

        $body = $response->getBody();
        $json = json_decode($body->getContents(), true);

        self::assertIsArray($json);
        self::assertCount(1, $json);

        $data = $json[0];

        $this->assertEquals($this->dummy_comment->getUserID(), $data['user_id']);
        $this->assertEquals($this->dummy_comment->getReviewID(), $data['review_id']);
        $this->assertEquals($this->dummy_comment->getText(), $data['text']);

        $this->assertArrayHasKey('comment_id', $data);
        $this->assertArrayHasKey('created_date', $data);
    }

    /**
     * @throws GuzzleException
     */
    public function testGetCommentById()
    {
        // test valid comment ID
        $response = self::$guzzle->get('comments/' . $this->dummy_comment->getCommentID());
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('comment_id', $data);
        $this->assertEquals($this->dummy_comment->getCommentID(), $data['comment_id']);

        // test invalid comment ID
        $response = self::$guzzle->get('comments/-1');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @throws GuzzleException
     */
    public function testCreateValidComment()
    {
        $new_comment = new Comment(
            user_id: $this->dummy_client->getUserID(),
            review_id: $this->dummy_review->getReviewID(),
            text: self::$faker->sentence()
        );

        $data_to_send = $new_comment->toArray();
        unset($data_to_send['comment_id']);
        unset($data_to_send['created_date']);

        $response = self::$guzzle->post('comments', [
            'json' => $data_to_send
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        $response_data = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('comment_id', $response_data);
        $this->assertEquals('Comment created successfully', $response_data['message']);
    }

    /**
     * @throws GuzzleException
     */
    public function testUpdateValidComment()
    {
        $updated_text = 'Updated comment text';
        $data_to_send = [
            'text' => $updated_text
        ];

        $response = self::$guzzle->put('comments/' . $this->dummy_comment->getCommentID(), [
            'json' => $data_to_send
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $response_data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Comment updated successfully', $response_data['message']);

        $updated_comment = Comment::getByID($this->dummy_comment->getCommentID());
        $this->assertEquals($updated_text, $updated_comment->getText());
    }

    /**
     * @throws GuzzleException
     */
    public function testDeleteComment()
    {
        $response = self::$guzzle->delete('comments/' . $this->dummy_comment->getCommentID());
        $this->assertEquals(204, $response->getStatusCode());

        $deleted_comment = Comment::getByID($this->dummy_comment->getCommentID());
        $this->assertNull($deleted_comment);
    }
}