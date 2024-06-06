<?php

declare(strict_types=1);

namespace Steamy\Tests\Model;

use Exception;
use PHPUnit\Framework\TestCase;
use Steamy\Model\Client;
use Steamy\Model\Order;
use Steamy\Model\OrderProduct;
use Steamy\Model\Product;
use Steamy\Model\Store;
use Steamy\Tests\helpers\TestHelper;
use Throwable;

class OrderProductTest extends TestCase
{
    use TestHelper;

    private ?Order $dummy_order;
    private ?Client $client;
    private ?Store $dummy_store;
    private ?Product $dummy_product;
    private array $line_items = [];

    public static function setUpBeforeClass(): void
    {
        self::initFaker();
        self::resetDatabase();
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
        // Initialize a dummy store object for testing
        $this->dummy_store = self::createStore();

        // Create a dummy client
        $this->client = self::createClient();

        // Create a dummy product
        $this->dummy_product = self::createProduct();

        // Update stock level for the product
        $this->dummy_store->addProductStock($this->dummy_product->getProductID(), 10);

        // Create dummy order line items
        $this->line_items = [
            new OrderProduct(
                $this->dummy_product->getProductID(), "medium", "oat", 2
            )
        ];

        // Create a dummy order
        $this->dummy_order = new Order(
            $this->dummy_store->getStoreID(),
            $this->client->getUserID()
        );

        // Add line items to the order
        foreach ($this->line_items as $line_item) {
            $this->dummy_order->addLineItem($line_item);
        }

        $success = $this->dummy_order->save();
        if (!$success) {
            throw new Exception('Unable to save order');
        }
    }

    public function tearDown(): void
    {
        $this->dummy_order = null;
        $this->client = null;
        $this->dummy_store = null;
        $this->dummy_product = null;
        $this->line_items = [];

        // Clear all data from relevant tables
        self::resetDatabase();
    }

    public function testValidate(): void
    {
        $invalidOrderProduct = new OrderProduct(
            product_id: $this->dummy_product->getProductID(),
            cup_size: "extra large",  // Invalid cup size
            milk_type: "invalid milk", // Invalid milk type
            quantity: -1,             // Invalid quantity
            unit_price: -2.99,        // Invalid unit price
            order_id: $this->dummy_order->getOrderID()
        );

        $errors = $invalidOrderProduct->validate();

        $this->assertArrayHasKey('quantity', $errors);
        $this->assertArrayHasKey('cup_size', $errors);
        $this->assertArrayHasKey('milk_type', $errors);
        $this->assertArrayHasKey('unit_price', $errors);
    }

    public function testGetById(): void
    {
        // Assuming getByID is a method that retrieves an OrderProduct by order ID and product ID
        $retrievedOrderProduct = OrderProduct::getByID(
            $this->dummy_order->getOrderID(),
            $this->dummy_product->getProductID()
        );

        $this->assertNotNull($retrievedOrderProduct);
        $this->assertEquals($this->dummy_order->getOrderID(), $retrievedOrderProduct->getOrderID());
        $this->assertEquals($this->dummy_product->getProductID(), $retrievedOrderProduct->getProductID());
        $this->assertEquals("medium", $retrievedOrderProduct->getCupSize());
        $this->assertEquals("oat", $retrievedOrderProduct->getMilkType());
        $this->assertEquals(2, $retrievedOrderProduct->getQuantity());
        $this->assertEquals($this->dummy_product->getPrice(), $retrievedOrderProduct->getUnitPrice());
    }
}
