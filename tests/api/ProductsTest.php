<?php

declare(strict_types=1);

namespace api;

use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client as GuzzleClient;
use Steamy\Core\Database;
use Steamy\Model\Product;

final class ProductsTest extends TestCase
{
    use Database;

    private ?GuzzleClient $client;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->client = new GuzzleClient([
            'base_uri' => $_ENV['API_BASE_URI']
        ]);

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
    }

    public function tearDown(): void
    {
        $this->client = null;
        self::query('DELETE FROM product;');
    }

    /**
     * @throws GuzzleException
     */
    public function testGetEndpoint()
    {
        $response = $this->client->get('products');
        $this->assertEquals(200, $response->getStatusCode());

        $body = $response->getBody();
        $json = json_decode((string)$body, true);
        echo json_encode($json, JSON_PRETTY_PRINT) . "\n";

//        $this->assertArrayHasKey('key', $data);
//        $this->assertEquals('expected_value', $data['key']);
    }

}