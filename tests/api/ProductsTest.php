<?php

declare(strict_types=1);

namespace Steamy\Tests\Api;

use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client as GuzzleClient;
use Steamy\Core\Database;
use Steamy\Model\Product;

final class ProductsTest extends TestCase
{
    use Database;

    private ?GuzzleClient $client;
    private Product $dummy_product;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        // Create a handler stack
        $handlerStack = HandlerStack::create();

        // Add middleware to the handler stack
        $handlerStack->push(Middleware::mapRequest(function ($request) {
            // Add custom header to each request
            return $request->withHeader('X-Test-Env', 'testing');
        }));

        $this->client = new GuzzleClient([
            'base_uri' => $_ENV['API_BASE_URI'],
            'http_errors' => false, // Optionally disable throwing exceptions for HTTP errors
            'handler' => $handlerStack,

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
    public function testGetAllProducts()
    {
        $response = $this->client->get('products');
        $this->assertEquals(200, $response->getStatusCode());

        $body = $response->getBody();
        $json = json_decode($body->getContents(), true);
//        echo json_encode($json, JSON_PRETTY_PRINT) . "\n";

        self::assertIsArray($json);
        self::assertCount(1, $json);

        $data = $json[0];

        $this->assertEquals($this->dummy_product->getName(), $data['name']);
        $this->assertEquals($this->dummy_product->getCalories(), $data['calories']);
        $this->assertEquals($this->dummy_product->getImgRelativePath(), $data['img_url']);
        $this->assertEquals($this->dummy_product->getCategory(), $data['category']);
        $this->assertEquals($this->dummy_product->getPrice(), $data['price']);
        $this->assertEquals($this->dummy_product->getDescription(), $data['description']);
        $this->assertEquals($this->dummy_product->getImgAltText(), $data['img_alt_text']);


        // only check presence of the following keys but not the actual value
        $this->assertArrayHasKey('product_id', $data);
        $this->assertArrayHasKey('created_date', $data);
    }


    /**
     * @throws GuzzleException
     */
    public function testGetProductById()
    {
        // test valid product ID
        $response = $this->client->get('products/' . $this->dummy_product->getProductID());
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('product_id', $data);
        $this->assertEquals($this->dummy_product->getProductID(), $data['product_id']);

        // test invalid product ID
        $response = $this->client->get('products/-1');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @throws GuzzleException
     */
    public function testGetProductCategories()
    {
        $response = $this->client->get('products/categories');
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertIsArray($data);
        self::assertCount(1, $data);
        self::assertEquals($this->dummy_product->getCategory(), $data[0]);
    }

    /**
     * @throws GuzzleException
     */
    public function testCreateProduct()
    {
        self::markTestIncomplete('Incomplete test');
        $response = $this->client->post('products', [
            'json' => [
                'name' => 'Test Product',
                'category' => 'Test Category',
                'price' => 99.99,
                // Add more fields as needed
            ]
        ]);
        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('id', $data);
        // Add more assertions as needed
    }

    public function testDeleteProductById()
    {
        self::markTestIncomplete('Incomplete test');
        $response = $this->client->delete('products/1');
        $this->assertEquals(204, $response->getStatusCode());
        // No content expected, so no further assertions needed
    }

    /**
     * @throws GuzzleException
     */
    public function testUpdateProductById()
    {
        self::markTestIncomplete('Incomplete test');
        $response = $this->client->put('products/1', [
            'json' => [
                'name' => 'Updated Product',
                'category' => 'Updated Category',
                'price' => 199.99,
                // Add more fields as needed
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals(1, $data['id']);
        // Add more assertions as needed
    }
}