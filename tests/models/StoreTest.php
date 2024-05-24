<?php

declare(strict_types=1);

namespace Steamy\Tests\Model;

use Exception;
use PHPUnit\Framework\TestCase;
use Steamy\Core\Database;
use Steamy\Model\Location;
use Steamy\Model\Store;

class StoreTest extends TestCase
{
    use Database;

    private ?Store $dummy_store;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        // Initialize a dummy store object for testing
        $this->dummy_store = new Store(
            phone_no: "12345678", // Phone number
            address: new Location(
                street: "Royal",
                city: "Curepipe",
                district_id: 1,
                latitude: 50,
                longitude: 50
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
    }

    public function tearDown(): void
    {
        // Clean up the dummy store object after each test
        if ($this->dummy_store) {
            $this->dummy_store = null;
        }

        // clear all data from store tables
        self::query('DELETE FROM store;');
    }

    /**
     * @dataProvider saveDataProvider
     * @param string $phone_no
     * @param Location $address
     * @param bool $expected_success
     * @throws Exception
     */
    public function testSave(string $phone_no, Location $address, bool $expected_success)
    {
        $this->dummy_store->setPhoneNo($phone_no);
        $this->dummy_store->setAddress($address);

        $success = $this->dummy_store->save();

        $this->assertEquals($expected_success, $success);
    }

    public static function saveDataProvider(): array
    {
        return [
            // Valid phone number, valid address
            ["1234567890", new Location("Royal", "Curepipe", 1, 50, 50), true],
            // Invalid phone number (less than 7 characters)
            ["123456", new Location("Royal", "Curepipe", 1, 50, 50), false],
            // Empty phone number
            ["", new Location("Royal", "Curepipe", 1, 50, 50), false],
            // Invalid characters in phone number
            ["123abc", new Location("Royal", "Curepipe", 1, 50, 50), false],
            // Valid address with valid latitude/longitude
            ["1234567890", new Location("Royal", "Curepipe", 1, 50, 50), true],
            // Invalid latitude value (out of range)
            ["1234567890", new Location("Royal", "Curepipe", 1, -100, 50), false],
        ];
    }

    /**
     * @dataProvider validateDataProvider
     * @param string $phone_no
     * @param Location $address
     * @param array $expected_errors
     */
    public function testValidate(string $phone_no, Location $address, array $expected_errors)
    {
        $this->dummy_store->setPhoneNo($phone_no);
        $this->dummy_store->setAddress($address);

        $errors = $this->dummy_store->validate();

        $this->assertEquals($expected_errors, $errors);
    }

    public static function validateDataProvider(): array
    {
        return [
            // Valid phone number, valid address (no errors)
            ["1234567890", new Location("Royal", "Curepipe", 1, 50, 50), []],
            // Invalid phone number (less than 7 characters)
            [
                "123456",
                new Location("Royal", "Curepipe", 1, 50, 50),
                ["phone_no" => "Phone number must be at least 7 characters long"]
            ],
            // Empty phone number
            [
                "",
                new Location("Royal", "Curepipe", 1, 50, 50),
                ["phone_no" => "Phone number must be at least 7 characters long"]
            ],
            // Invalid characters in phone number
            [
                "123abc",
                new Location("Royal", "Curepipe", 1, 50, 50),
                ["phone_no" => "Phone number must be at least 7 characters long"]
            ],
            // Invalid address with invalid latitude/longitude
            [
                "1234567890",
                new Location("Royal", "Curepipe", 1, -100, 50),
                ["coordinates" => "Invalid latitude or longitude."]
            ],
        ];
    }
}
