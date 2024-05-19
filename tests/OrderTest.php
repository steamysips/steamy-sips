<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Model\Order;
use Steamy\Model\OrderProduct;
use Steamy\Model\OrderStatus;
use Steamy\Model\Store;
use Steamy\Model\Client;
use Steamy\Core\Database;
use Steamy\Model\Location;
use Steamy\Model\Product;

class OrderTest extends TestCase
{
    use Database;

    private ?Order $dummy_order = null;
    private ?Client $client = null;
    private ?Store $dummy_store = null;
    private array $line_items = [];

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
        $this->client = new Client("john@example.com", "John", "Doe", "john_doe", "password", new Location("Royal", "Curepipe", 1, 50, 50));
        $success = $this->client->save();
        if (!$success) {
            throw new Exception('Unable to save client');
        }

        // Create dummy products
        $product1 = new Product("Latte", 50, "latte.jpeg", "A delicious latte", "Beverage", 5.0, "A cup of latte", new DateTime());
        $success = $product1->save();
        if (!$success) {
            throw new Exception('Unable to save product 1');
        }

        $product2 = new Product("Espresso", 30, "espresso.jpeg", "A strong espresso", "Beverage", 3.0, "A cup of espresso", new DateTime());
        $success = $product2->save();
        if (!$success) {
            throw new Exception('Unable to save product 2');
        }

        // Create dummy order line items
        $this->line_items = [
            new OrderProduct($product1->getProductID(), "medium", "oat", 2, 5.0),
            new OrderProduct($product2->getProductID(), "small", "almond", 1, 3.0)
        ];

        // Create a dummy order
        $this->dummy_order = new Order(
            $this->dummy_store->getStoreID(),
            $this->client->getUserID(),
            $this->line_items
        );
    }

    public function tearDown(): void
    {
        $this->dummy_order = null;
        $this->client = null;
        $this->dummy_store = null;
        $this->line_items = [];

        // Clear all data from relevant tables
        self::query('DELETE FROM order_product; DELETE FROM `order`; DELETE FROM client; DELETE FROM user; DELETE FROM store_product; DELETE FROM product; DELETE FROM store;');
    }

    public function testConstructor(): void
    {
        $new_order = new Order(
            $this->dummy_store->getStoreID(),
            $this->client->getUserID(),
            $this->line_items
        );

        self::assertEquals($this->dummy_store->getStoreID(), $new_order->getStoreID());
        self::assertEquals($this->client->getUserID(), $new_order->getClientID());
        self::assertEquals(OrderStatus::PENDING, $new_order->getStatus());
        self::assertEquals($this->line_items, $new_order->getLineItems());
    }

    public function testToArray(): void
    {
        $result = $this->dummy_order->toArray();

        self::assertArrayHasKey('order_id', $result);
        self::assertArrayHasKey('status', $result);
        self::assertArrayHasKey('created_date', $result);
        self::assertArrayHasKey('pickup_date', $result);
        self::assertArrayHasKey('client_id', $result);
        self::assertArrayHasKey('store_id', $result);

        self::assertEquals($this->dummy_order->getOrderID(), $result['order_id']);
        self::assertEquals($this->dummy_order->getStatus()->value, $result['status']);
        self::assertEquals($this->dummy_order->getCreatedDate()->format('Y-m-d H:i:s'), $result['created_date']);
        self::assertEquals($this->dummy_order->getPickupDate()?->format('Y-m-d H:i:s'), $result['pickup_date']);
        self::assertEquals($this->dummy_order->getClientID(), $result['client_id']);
        self::assertEquals($this->dummy_order->getStoreID(), $result['store_id']);
    }

    public function testSave(): void
    {
        $success = $this->dummy_order->save();
        self::assertTrue($success);

        $order_id = $this->dummy_order->getOrderID();
        self::assertGreaterThan(0, $order_id);

        // Verify order in database
        $saved_order = Order::getByID($order_id);
        self::assertNotNull($saved_order);
        self::assertEquals($this->dummy_order->getStoreID(), $saved_order->getStoreID());
        self::assertEquals($this->dummy_order->getClientID(), $saved_order->getClientID());
    }

    public function testSaveWithEmptyLineItems(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cart cannot be empty');

        $order = new Order($this->dummy_store->getStoreID(), $this->client->getUserID(), []);
        $order->save();
    }

    public function testAddLineItem(): void
    {
        $order = new Order($this->dummy_store->getStoreID(), $this->client->getUserID());
        $order->addLineItem(new OrderProduct(1, "medium", "oat", 1, 5.0));
        self::assertCount(1, $order->getLineItems());
    }

    public function testGetByID(): void
    {
        $this->dummy_order->save();
        $order_id = $this->dummy_order->getOrderID();

        $fetched_order = Order::getByID($order_id);
        self::assertNotNull($fetched_order);

        self::assertEquals($this->dummy_order->getStoreID(), $fetched_order->getStoreID());
        self::assertEquals($this->dummy_order->getClientID(), $fetched_order->getClientID());
        self::assertEquals($this->dummy_order->getStatus(), $fetched_order->getStatus());

        // Test getByID with invalid ID
        self::assertNull(Order::getByID(-1));
    }

    public function testCalculateTotalPrice(): void
    {
        $this->dummy_order->save();
        $total_price = $this->dummy_order->calculateTotalPrice();

        $expected_price = array_reduce($this->line_items, function ($carry, $item) {
            return $carry + $item->getQuantity() * $item->getUnitPrice();
        }, 0);

        self::assertEquals($expected_price, $total_price);
    }

    public function testValidate(): void
    {
        $errors = $this->dummy_order->validate();
        self::assertEmpty($errors);
    }
}
