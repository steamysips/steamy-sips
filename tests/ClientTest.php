<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Model\Client;
use Steamy\Model\Location;
use Steamy\Core\Database;


final class ClientTest extends TestCase
{
    use Database;
    private ?Client $dummy_client;

    public function setUp(): void
    {
        $address = new Location("Royal Road", "Curepipe", 1);
        $this->dummy_client = new Client(
            "john_u@gmail.com",
            "john",
            "johhny",
            "abcd",
            "13213431",
            $address
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
        self::query('DELETE FROM client; DELETE FROM user;');
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
            "",
            "",
            "",
            "abcd",
            "",
            new Location(), // pass an empty Location object for testing
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
            "a@a.com",
            "Jo",
            "Doe",
            "1234567",
            "123456",
            new Location(), // pass an empty Location object for testing
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

    /**
     * @dataProvider getByIDProvider
     */
    public static function testGetByID(int $userID, ?string $expectedEmail): void
    {
        $client = Client::getByID($userID);
        if ($expectedEmail !== null) {
            self::assertNotNull($client);
            self::assertEquals($expectedEmail, $client->getEmail());
        } else {
            self::assertNull($client);
        }
    }

    public static function getByIDProvider(): array
    {
        return [
            [999, null], // Non-existing user
            [-1, null], // Negative ID
        ];
    }

    /**
     * @dataProvider getByEmailProvider
     */
    public static function testGetByEmail(string $email, ?string $expectedEmail): void
    {
        $client = Client::getByEmail($email);
        if ($expectedEmail !== null) {
            self::assertNotNull($client);
            self::assertEquals($expectedEmail, $client->getEmail());
        } else {
            self::assertNull($client);
        }
    }

    public static function getByEmailProvider(): array
    {
        return [
            ['john_u@gmail.com', 'john_u@gmail.com'], // Existing email
            ['nonexistent@gmail.com', null], // Non-existing email
            ['invalidemail', null], // Invalid email format
        ];
    }

    /**
     * @dataProvider updateUserProvider
     */
    public static function testUpdateUser(bool $updatePassword, bool $success): void
    {
        // Create a client with a known ID
        $client = Client::getByEmail('john_u@gmail.com');
        if ($client === null) {
            self::fail('Failed to fetch client');
        }

        // Update user and check if successful
        $client->setFirstName('UpdatedName');
        $client->setLastName('UpdatedLastName');
        $client->getAddress()->setCity('UpdatedCity');

        if ($updatePassword) {
            $client->setPassword('newPassword');
        }

        $result = $client->updateUser($updatePassword);
        self::assertEquals($success, $result);

        // Check if data was actually updated in the database
        $updatedClient = Client::getByID($client->getUserID());
        if ($updatedClient === null) {
            self::fail('Failed to fetch updated client');
        }

        self::assertEquals('UpdatedName', $updatedClient->getFirstName());
        self::assertEquals('UpdatedLastName', $updatedClient->getLastName());
        self::assertEquals('UpdatedCity', $updatedClient->getAddress()->getCity());
    }

    public static function updateUserProvider(): array
    {
        return [
            [false, true], // Update without password change
            [true, true],  // Update with password change
        ];
    }

    public function testDeleteUser(): void
    {
        // Fetch the client by email to get its ID
        $client = Client::getByEmail('john_u@gmail.com');
        if ($client === null) {
            self::fail('Failed to fetch client');
        }

        // Delete the user
        $client->deleteUser();

        // Attempt to fetch the user again
        $deletedClient = Client::getByID($client->getUserID());

        // Ensure the user does not exist anymore
        self::assertNull($deletedClient);
    }
}
