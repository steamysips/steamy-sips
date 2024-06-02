<?php

declare(strict_types=1);

namespace Steamy\Tests\Api;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Steamy\Model\Product;
use Steamy\Tests\helpers\APIHelper;
use Steamy\Tests\helpers\TestHelper;
use Throwable;

final class ProductsTest extends TestCase
{
    use TestHelper;
    use APIHelper;

    private Product $dummy_product;

    public static function setUpBeforeClass(): void
    {
        self::initFaker();
        self::initGuzzle();
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
        $this->dummy_product = self::createProduct();
    }

    public function tearDown(): void
    {
        self::resetDatabase();
    }

    /**
     * @throws GuzzleException
     */
    public function testGetAllProducts()
    {
        $response = self::$guzzle->get('products');
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
        $response = self::$guzzle->get('products/' . $this->dummy_product->getProductID());
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('product_id', $data);
        $this->assertEquals($this->dummy_product->getProductID(), $data['product_id']);

        // test invalid product ID
        $response = self::$guzzle->get('products/-1');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @throws GuzzleException
     */
    public function testGetProductCategories()
    {
        $response = self::$guzzle->get('products/categories');
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
        $response = self::$guzzle->post('products', [
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
        $response = self::$guzzle->delete('products/1');
        $this->assertEquals(204, $response->getStatusCode());
        // No content expected, so no further assertions needed
    }

    /**
     * @throws GuzzleException
     */
    public function testUpdateProductById()
    {
        self::markTestIncomplete('Incomplete test');
        $response = self::$guzzle->put('products/1', [
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