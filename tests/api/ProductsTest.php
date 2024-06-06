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

use function PHPUnit\Framework\assertEquals;

final class ProductsTest extends TestCase
{
    use TestHelper;
    use APIHelper;

    private Product $dummy_product;

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
     * @throws Exception Expected product could not be created
     */
    public function testCreateValidProduct()
    {
        $expected_product = self::createProduct(false);

        $data_to_send = $expected_product->toArray();
        unset($data_to_send['product_id']);
        unset($data_to_send['created_date']);

//        self::log_json($data_to_send);

        $response = self::$guzzle->post(
            'products',
            ['json' => $data_to_send]
        );

        $data_received = json_decode($response->getBody()->getContents(), true);
//        self::log_json($data_received);

        $this->assertEquals(201, $response->getStatusCode());

        $this->assertArrayHasKey('product_id', $data_received);
        self::assertTrue($data_received['product_id'] > 0);
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testDeleteProductById()
    {
        // delete a non-existent product
        $response = self::$guzzle->delete('products/0');
        $this->assertEquals(404, $response->getStatusCode());

        // delete a valid product
        $product = self::createProduct();
        $response = self::$guzzle->delete('products/' . $product->getProductID());
        $this->assertEquals(204, $response->getStatusCode());
        self::assertNull(Product::getByID($product->getProductID()));
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testUpdateProductByIdForInvalidProduct()
    {
        $response = self::$guzzle->put('products/0');
        $this->assertEquals(404, $response->getStatusCode());

        $response = self::$guzzle->put('products/-43');
        $this->assertEquals(404, $response->getStatusCode());
    }


    public static function provideNewProductData(): array
    {
        return [
            'new name' => [
                'new_data' => [
                    'name' => 'dsajd'
                ],
                'changed_data' => [
                    'name' => 'dsajd'
                ]
            ],
            'new product id' => [
                'new_data' => [
                    'product_id' => 444
                ],
                'changed_data' => [
                    'product_id' => null
                ]
            ],
            'new name and description' => [
                'new_data' => [
                    'name' => 'my new name',
                    'description' => 'new description'
                ],
                'changed_data' => [
                    'name' => 'my new name',
                    'description' => 'new description'
                ]
            ]
        ];
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     * @dataProvider  provideNewProductData
     */
    public function testUpdateProductByIdForValidProduct(array $new_data, array $expected_data)
    {
        // save a valid product to database
        $old_product = self::createProduct();
        $old_data = $old_product->toArray();

        // update the name of a valid product
        $response = self::$guzzle->put(
            'products/' . $old_product->getProductID(),
            ['json' => $new_data]
        );

        if (array_key_exists('product_id', $new_data)) {
            // if request attempts to modify product ID, request should be rejected
            $this->assertEquals(400, $response->getStatusCode());

            // ensure that original product was not modified
            $fetched_product = Product::getByID($old_product->getProductID());
            self::assertNotNull($fetched_product);

            $fetched_data = $fetched_product->toArray();

            unset($old_data['created_date']);
            unset($fetched_data['created_date']);

            assertEquals($old_data, $fetched_data);

            return;
        }

        // else request is valid

        $this->assertEquals(200, $response->getStatusCode());

        // fetch same product directly from database
        $fetched_product = Product::getByID($old_product->getProductID());
        self::assertNotNull($fetched_product);
        $fetched_data = $fetched_product->toArray();

        foreach (array_keys($expected_data) as $key) {
            if ($expected_data[$key] === null) {
                // data corresponding to key must not change
                $this->assertEquals($old_data[$key], $fetched_data[$key]);
            } else {
                // data corresponding to key must change
                $this->assertEquals($expected_data[$key], $fetched_data[$key]);
            }
        }
    }
}