<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Model\Client;
use \Steamy\Model\District;

final class ClientTest extends TestCase
{

    public function testConstructor(): void
    {
        $client = new Client(
            "john@gmail.com", "john", "johhny", "abcd",
            "11", new District(1), "Royal Road", "Curepipe"
        );

        // check if fields were correctly set
        self::assertEquals("john@gmail.com", $client->getEmail());
        self::assertEquals("john", $client->getFirstName());
        self::assertEquals("johhny", $client->getLastName());
        self::assertEquals("11", $client->getPhoneNo());
        self::assertEquals("Royal Road, Curepipe, Moka", $client->getAddress());
    }

    public function testToArray(): void
    {
        $client = new Client(
            "john@gmail.com", "john", "johhny", "abcd",
            "11", new District(1), "Royal Road", "Curepipe"
        );

        $result = $client->toArray();

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
        self::assertEquals("11", $result['phone_no']);
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
        $client = new Client(
            "john@gmail.com", "john", "johhny", "abcd",
            "11", new District(1), "Royal Road", "Curepipe"
        );

        // verify true password
        $this->assertTrue($client->verifyPassword("abcd"));

        // reject empty string
        $this->assertNotTrue($client->verifyPassword(""));

        // reject any other string
        $this->assertNotTrue($client->verifyPassword("abcde"));
        $this->assertNotTrue($client->verifyPassword("abcd "));
        $this->assertNotTrue($client->verifyPassword(" abcd"));
    }
}