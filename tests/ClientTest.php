<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Model\Client;
use Steamy\Model\District;

final class ClientTest extends TestCase
{
    private ?Client $dummy_client;

    public function setUp(): void
    {
        $district = District::getByID(1);
        $this->dummy_client = new Client(
            "john_u@gmail.com", "john", "johhny", "abcd",
            "13213431", $district, "Royal Road", "Curepipe"
        );
    }

    public function tearDown(): void
    {
        $this->dummy_client = null;
    }

    public function testConstructor(): void
    {
        // check if fields were correctly set
        self::assertEquals(-1, $this->dummy_client->getUserID());
        self::assertEquals("john_u@gmail.com", $this->dummy_client->getEmail());
        self::assertEquals("john", $this->dummy_client->getFirstName());
        self::assertEquals("johhny", $this->dummy_client->getLastName());
        self::assertEquals("13213431", $this->dummy_client->getPhoneNo());
        self::assertEquals("Royal Road, Curepipe, Moka", $this->dummy_client->getAddress());
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
        $this->assertArrayHasKey('district', $result);
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
        self::assertEquals("Moka", $result['district']);
    }

    public function testValidate()
    {
        $client = new Client(
            "", "", "", "abcd",
            "", new District(1, 'Sample District'), "", ""
        );
        // test if existence checks work
        self::assertEquals([
            'email' => 'Invalid email format',
            'first_name' => 'First name must be at least 3 characters long',
            'last_name' => 'Last name must be at least 3 characters long',
            'phone_no' => 'Phone number must be at least 7 characters long',
            'city' => 'City name must have at least 3 characters',
            'street' => 'Street name must have at least 4 characters',
            'district' => 'District does not exist'
        ],
            $client->validate());

        $this->markTestIncomplete(
            'This test lacks range checks, ...',
        );
    }

    public function testVerifyPassword()
    {
        // verify true password
        $this->assertTrue($this->dummy_client->verifyPassword("abcd"));

        // reject empty string
        $this->assertNotTrue($this->dummy_client->verifyPassword(""));

        // reject any other string
        $this->assertNotTrue($this->dummy_client->verifyPassword("abcde"));
        $this->assertNotTrue($this->dummy_client->verifyPassword("abcd "));
        $this->assertNotTrue($this->dummy_client->verifyPassword(" abcd"));
    }

    public function testGetByEmail()
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
        self::assertEquals("Royal Road, Curepipe, Moka", $fetched_client->getAddress());

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
