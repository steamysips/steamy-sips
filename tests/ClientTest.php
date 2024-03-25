<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Model\Client;
use \Steamy\Model\District;

final class ClientTest extends TestCase
{
    private ?Client $dummy_client;

    public function setUp(): void
    {
        $this->dummy_client = new Client(
            "john@gmail.com", "john", "johhny", "abcd",
            "13213431", new District(1), "Royal Road", "Curepipe"
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
        self::assertEquals("john@gmail.com", $this->dummy_client->getEmail());
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
        self::assertEquals("john@gmail.com", $result['email']);
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
            "", new District(1), "", ""
        );
        // test if existence checks work
        self::assertEquals([
            'email' => 'Email is required',
            'first_name' => 'First name is required',
            'phone_no' => 'Phone number is required',
            'last_name' => 'Last name is required',
            'city' => 'City name is required',
            'street' => 'Street name is required'
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
        // test for valid email

        // save dummy record to database
        $this->dummy_client->save();

        $fetched_client = Client::getByEmail($this->dummy_client->getEmail());
        self::assertTrue($fetched_client->getUserID() > -1);
        self::assertEquals("john@gmail.com", $fetched_client->getEmail());
        self::assertEquals("john", $fetched_client->getFirstName());
        self::assertEquals("johhny", $fetched_client->getLastName());
        self::assertEquals("13213431", $fetched_client->getPhoneNo());
        self::assertEquals("Royal Road, Curepipe, Moka", $fetched_client->getAddress());

        // delete dummy record
        $fetched_client->deleteUser();

        // test for invalid email
        $fetched_client = Client::getByEmail("john@gmail.com");
        self::assertFalse($fetched_client);
    }
}