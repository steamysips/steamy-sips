<?php

declare(strict_types=1);

namespace Steamy\Tests\Model;

use Exception;
use PHPUnit\Framework\TestCase;
use Steamy\Core\Database;
use Steamy\Model\Client;
use Steamy\Model\Location;
use Steamy\Tests\helpers\TestHelper;


final class ClientTest extends TestCase
{
    use TestHelper;

    private ?Client $dummy_client;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $address = new Location("Royal Road", "Curepipe", 1);
        $this->dummy_client = new Client(
            "john_u@gmail.com", "john", "johhny", "abcd",
            "13213431", $address
        );

        $success = $this->dummy_client->save();
        if (!$success) {
            throw new Exception('Unable to save client');
        }
    }

    public function tearDown(): void
    {
        $this->dummy_client = null;

        // Clear all data from client and user tables
        self::resetDatabase();
    }

    public function testConstructor(): void
    {
        // check if fields were correctly set
        self::assertEquals("john_u@gmail.com", $this->dummy_client->getEmail());
        self::assertEquals("john", $this->dummy_client->getFirstName());
        self::assertEquals("johhny", $this->dummy_client->getLastName());
        self::assertEquals("13213431", $this->dummy_client->getPhoneNo());
        self::assertEquals("Royal Road, Curepipe, Moka", $this->dummy_client->getAddress()->getFormattedAddress());
    }

    public function testToArray(): void
    {
        $result = $this->dummy_client->toArray();

        // check if all required keys are present
        $this->assertArrayHasKey('user_id', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('first_name', $result);
        $this->assertArrayHasKey('last_name', $result);
        $this->assertArrayHasKey('phone_no', $result);
        $this->assertArrayHasKey('district_id', $result);
        $this->assertArrayHasKey('street', $result);
        $this->assertArrayHasKey('city', $result);
        $this->assertArrayHasKey('password', $result);

        // check if actual values are correct
        self::assertEquals("john_u@gmail.com", $result['email']);
        self::assertEquals("john", $result['first_name']);
        self::assertEquals("johhny", $result['last_name']);
        self::assertEquals("13213431", $result['phone_no']);
        self::assertEquals("Royal Road", $result['street']);
        self::assertEquals("Curepipe", $result['city']);
        self::assertEquals(1, $result['district_id']);
    }

    public function testValidate(): void
    {
        $client = new Client(
            "", "", "", "abcd",
            "", new Location(), // pass an empty Location object for testing
        );

        // Test if existence checks work
        self::assertEquals([
            'email' => 'Invalid email format',
            'first_name' => 'First name must be at least 3 characters long',
            'last_name' => 'Last name must be at least 3 characters long',
            'phone_no' => 'Phone number must be at least 7 characters long',
            'district' => 'District does not exist'
        ], $client->validate());

        // Test for range checks
        $client = new Client(
            "a@a.com", "Jo", "Doe", "1234567",
            "123456", new Location(), // pass an empty Location object for testing
        );

        self::assertEquals([
            'first_name' => 'First name must be at least 3 characters long',
            'phone_no' => 'Phone number must be at least 7 characters long',
            'district' => 'District does not exist'
        ], $client->validate());
    }

    public function testVerifyPassword(): void
    {
        // verify true password
        self::assertTrue($this->dummy_client->verifyPassword("abcd"));

        // reject empty string
        self::assertFalse($this->dummy_client->verifyPassword(""));

        // reject any other string
        self::assertFalse($this->dummy_client->verifyPassword("abcde"));
        self::assertFalse($this->dummy_client->verifyPassword("abcd "));
        self::assertFalse($this->dummy_client->verifyPassword(" abcd"));
    }

    public function testGetByEmail(): void
    {
        // Test for valid email
        // Save the dummy record to the database
        $this->dummy_client->save();
        // Fetch the client by email
        $fetched_client = Client::getByEmail($this->dummy_client->getEmail());
        // Assert that the fetched client is not null
        self::assertNotNull($fetched_client);

        // Assert the attributes of the fetched client
        self::assertEquals("john_u@gmail.com", $fetched_client->getEmail());
        self::assertEquals("john", $fetched_client->getFirstName());
        self::assertEquals("johhny", $fetched_client->getLastName());
        self::assertEquals("13213431", $fetched_client->getPhoneNo());
        self::assertEquals("Royal Road, Curepipe, Moka", $fetched_client->getAddress()->getFormattedAddress());

        // Delete the dummy record
        $fetched_client->deleteUser();

        // Add a small delay to ensure the deletion operation is completed
        usleep(500000); // 500 milliseconds = 0.5 seconds

        // Fetch the client by email again
        $fetched_client = Client::getByEmail($this->dummy_client->getEmail());

        // Test for invalid email
        // Assert that the fetched client is null or false
        self::assertNull($fetched_client);
    }
}
