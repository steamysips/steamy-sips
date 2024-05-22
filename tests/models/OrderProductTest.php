<?php

declare(strict_types=1);

namespace models;

use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use Steamy\Core\Database;
use Steamy\Model\Client;
use Steamy\Model\Location;
use Steamy\Model\Order;
use Steamy\Model\OrderProduct;
use Steamy\Model\Product;
use Steamy\Model\Store;

class OrderProductTest extends TestCase
{
    use Database;

    private ?Order $dummy_order;
    private ?Client $client;
    private ?Store $dummy_store;
    private ?Product $dummy_product;
    private array $line_items = [];

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // Initialize a dummy store object for testing
        $this->dummy_store = new Store(
            phone_no: "987654321", // Phone number
            address: new Location(
                street: "Augus",
                city: "Flacq",
                district_id: 2,
                latitude: 60,
                longitude: 60
            )
        );

        $success = $this->dummy_store->save();
        if (!$success) {
            $errors = $this->dummy_store->validate();
            $error_msg = "Unable to save store to database. ";
            if (!empty($errors)) {
                $error_msg .= "Errors: " . implode(',', $errors);
            } else {
                $error_msg .= "Attributes seem to be ok as per validate().";
            }

            throw new Exception($error_msg);
        }

        // Create a dummy client
        $this->client = new Client(
            "john@example.com",
            "John",
            "Doe",
            "john_doe",
            "password",
            new Location("Royal", "Curepipe", 1, 50, 50)
        );
        $success = $this->client->save();
        if (!$success) {
            throw new Exception('Unable to save client');
        }

        // Create a dummy product
        $this->dummy_product = new Product(
            "Latte",
            50,
            "latte.jpeg",
            "A delicious latte",
            "Beverage",
            5.0,
            "A cup of latte",
            new DateTime()
        );
        $success = $this->dummy_product->save();
        if (!$success) {
            throw new Exception('Unable to save product');
        }

        // Update stock level for the product
        $this->dummy_store->addProductStock($this->dummy_product->getProductID(), 10);

        // Create dummy order line items
        $this->line_items = [
            new OrderProduct($this->dummy_product->getProductID(), "medium", "oat", 2, 5.0)
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
        self::query(
            'DELETE FROM order_product; DELETE FROM `order`; DELETE FROM client; DELETE FROM user; DELETE FROM store_product; DELETE FROM product; DELETE FROM store;'
        );
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
        $this->assertEquals(5.0, $retrievedOrderProduct->getUnitPrice());
    }
}
