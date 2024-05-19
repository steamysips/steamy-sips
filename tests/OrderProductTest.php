<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Model\Order;
use Steamy\Model\OrderProduct;
use Steamy\Model\Store;
use Steamy\Model\Client;
use Steamy\Model\Product;
use Steamy\Core\Database;
use Steamy\Model\Location;

class OrderProductTest extends TestCase
{
    use Database;

    private ?Order $dummy_order;
    private ?Client $client;
    private ?Store $dummy_store;
    private ?Product $dummy_product;
    private ?OrderProduct $orderProduct;

    public function setUp(): void
    {
        parent::setUp();
        
        // Initialize a dummy store object for testing
        $this->dummy_store = new Store(
            phone_no: "987654321", 
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
        $this->client = new Client("john@example.com", "John", "Doe", "john_doe", "password", new Location("Royal", "Curepipe", 1, 50, 50));
        $success = $this->client->save();
        if (!$success) {
            throw new Exception('Unable to save client');
        }

        // Create dummy products
        $this->dummy_product = new Product("Latte", 50, "latte.jpeg", "A delicious latte", "Beverage", 5.0, "A cup of latte", new DateTime());
        $success = $this->dummy_product->save();
        if (!$success) {
            throw new Exception('Unable to save product');
        }

        // Create a dummy order
        $this->dummy_order = new Order($this->dummy_store->getStoreID(), $this->client->getUserID());
        $success = $this->dummy_order->save();
        if (!$success) {
            throw new Exception('Unable to save order');
        }

        // Create dummy orderProduct
        $this->orderProduct = new OrderProduct(
            product_id: $this->dummy_product->getProductID(), 
            cup_size: "medium", 
            milk_type: "oat", 
            quantity: 2, 
            unit_price: 2.99,
            order_id: $this->dummy_order->getOrderID()
        );
        $success = $this->orderProduct->save();
        if (!$success) {
            throw new Exception('Unable to save order product');
        }
    }

    public function tearDown(): void
    {
        $this->dummy_order = null;
        $this->client = null;
        $this->dummy_store = null;
        $this->dummy_product = null;
        $this->orderProduct = null;

        // Clear all data from relevant tables
        self::query('DELETE FROM order_product; DELETE FROM `order`; DELETE FROM client; DELETE FROM user; DELETE FROM store_product; DELETE FROM product; DELETE FROM store;');
    }

    public function testValidate(): void
    {
        $invalidOrderProduct = new OrderProduct(
            product_id: $this->dummy_product->getProductID(),
            cup_size: "extra large",  // Invalid cup size
            milk_type: "cow",         // Invalid milk type
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

    public function testGetByID(): void
    {
        $retrievedOrderProduct = OrderProduct::getByID($this->dummy_order->getOrderID(), $this->dummy_product->getProductID());

        $this->assertNotNull($retrievedOrderProduct);
        $this->assertEquals($this->dummy_order->getOrderID(), $retrievedOrderProduct->getOrderID());
        $this->assertEquals($this->dummy_product->getProductID(), $retrievedOrderProduct->getProductID());
        $this->assertEquals("medium", $retrievedOrderProduct->getCupSize());
        $this->assertEquals("oat", $retrievedOrderProduct->getMilkType());
        $this->assertEquals(2, $retrievedOrderProduct->getQuantity());
        $this->assertEquals(2.99, $retrievedOrderProduct->getUnitPrice());
    }
}
