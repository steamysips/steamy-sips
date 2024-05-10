<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Model\Store;
use Steamy\Model\Location;
use Steamy\Core\Database;

class StoreTest extends TestCase
{
    use Database;
    private ?Store $dummy_store;

    public function setUp(): void
    {
        parent::setUp();

        // Initialize a dummy store object for testing
        $this->dummy_store = new Store(
            phone_no: "12345678", // Phone number
            address: new Location("Royal", "Curepipe", 1) // Address
        );
        
        $success = $this->dummy_store->save();
        if (!$success) {
            throw new Exception('Unable to save store');
        }

    }

    public function tearDown(): void
    {
        // Clean up the dummy store object after each test
        if ($this->dummy_store) {
            $this->dummy_store = null;
        }

        // clear all data from store tables
        self::query('DELETE FROM  store_product; DELETE FROM store;');
    }

    public function testSave(): void
    {
        // Test saving a valid store
        $saved = $this->dummy_store->save();
        self::assertTrue($saved);

        // Test saving an invalid store (phone number too short)
        $this->dummy_store->setPhoneNo('123'); // Short phone number
        $saved = $this->dummy_store->save();
        self::assertFalse($saved);
    }

    public function testValidate(): void
    {
        // Test for a valid store
        $errors = $this->dummy_store->validate();
        self::assertEmpty($errors);

        // Test for an invalid phone number (too short)
        $this->dummy_store->setPhoneNo('123'); // Short phone number
        $errors = $this->dummy_store->validate();
        self::assertArrayHasKey('phone_no', $errors);
    }

    public function testGetProductStock(): void
    {
        // Assuming product_id 1 is present in the store
        $product_id = 1;
        $stock_level = $this->dummy_store->getProductStock($product_id);
        self::assertGreaterThanOrEqual(0, $stock_level);
    }
}
