<?php

declare(strict_types=1);

namespace Steamy\Tests\Model;

use Exception;
use PHPUnit\Framework\TestCase;
use Steamy\Model\Administrator;
use Steamy\Tests\helpers\TestHelper;

final class AdministratorTest extends TestCase
{
    use TestHelper;

    private ?Administrator $dummy_admin;

    public static function setUpBeforeClass(): void
    {
        self::resetDatabase();
    }

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        // Create an administrator object and save it to the database
        $this->dummy_admin = new Administrator(
            "john@gmail.com", "john", "prince", "abcd",
            "13213431", "Manager", false
        );

        $success = $this->dummy_admin->save();
        if (!$success) {
            throw new Exception('Unable to save administrator');
        }
    }

    public function tearDown(): void
    {
        // Clear the administrator object
        $this->dummy_admin = null;
        self::resetDatabase();
    }

    public function testConstructor(): void
    {
        $new_admin = new Administrator(
            "john@gmail.com", "john", "prince", "abcd",
            "13213431", "Manager", false
        );

        // check if constructor has properly set all fields
        self::assertEquals(-1, $new_admin->getUserID());
        self::assertEquals("john@gmail.com", $new_admin->getEmail());
        self::assertEquals("john", $new_admin->getFirstName());
        self::assertEquals("prince", $new_admin->getLastName());
        self::assertEquals("13213431", $new_admin->getPhoneNo());
        self::assertFalse($new_admin->isSuperAdmin());
    }

    public function testToArray(): void
    {
        $result = $this->dummy_admin->toArray();

        // check if all required keys are present
        $this->assertArrayHasKey('user_id', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('first_name', $result);
        $this->assertArrayHasKey('last_name', $result);
        $this->assertArrayHasKey('phone_no', $result);
        $this->assertArrayHasKey('is_super_admin', $result);
        $this->assertArrayHasKey('password', $result);
        $this->assertArrayHasKey('job_title', $result);

        // check if actual values are correct
        self::assertTrue($result['user_id'] > 0);
        self::assertEquals("john@gmail.com", $result['email']);
        self::assertEquals("john", $result['first_name']);
        self::assertEquals("prince", $result['last_name']);
        self::assertEquals("13213431", $result['phone_no']);
        self::assertEquals("Manager", $result['job_title']);
        self::assertTrue($result['is_super_admin'] == 0);
    }

    public function testValidate(): void
    {
        $administrator = new Administrator(
            "", "", "", "abcd",
            "", "", false
        );
        // test if existence checks work
        self::assertEquals([
            'email' => 'Invalid email format',
            'first_name' => 'First name must be at least 3 characters long',
            'phone_no' => 'Phone number must be at least 7 characters long',
            'last_name' => 'Last name must be at least 3 characters long',
            'job_title' => 'Job title must be longer than 3 characters'
        ], $administrator->validate());

        // Test range checks
        $administrator = new Administrator(
            "a@b.com", "Jo", "Doe", "abcd",
            "123456", "Man", false
        );
        self::assertEquals([
            'first_name' => 'First name must be at least 3 characters long',
            'phone_no' => 'Phone number must be at least 7 characters long',
            'job_title' => 'Job title must be longer than 3 characters'
        ], $administrator->validate());

        // Test valid inputs
        $administrator = new Administrator(
            "a@b.com", "John", "Doe", "abcd",
            "1234567", "Manager", false
        );
        self::assertEmpty($administrator->validate());
    }

    /**
     * @dataProvider verifyPasswordDataProvider
     */
    public function testVerifyPassword($plain_password, $valid)
    {
        // verify true password of dummy admin
        self::assertEquals($this->dummy_admin->verifyPassword($plain_password), $valid);
    }


    public static function verifyPasswordDataProvider(): array
    {
        return [
            ["abcd", true],
            ["", false],
            ["abcde", false],
            ["abcd ", false],
            [" abcd", false]
        ];
    }

    public function testGetByEmail(): void
    {
        // Get the administrator by email
        $retrieved_admin = Administrator::getByEmail($this->dummy_admin->getEmail());

        // Assert that the retrieved administrator is not null
        $this->assertNotNull($retrieved_admin);

        // ensure that all attributes were properly fetched from database
        self::assertEquals($retrieved_admin->getUserID(), $this->dummy_admin->getUserID());
        self::assertEquals($retrieved_admin->getEmail(), $this->dummy_admin->getEmail());
        self::assertEquals($retrieved_admin->getFirstName(), $this->dummy_admin->getFirstName());
        self::assertEquals($retrieved_admin->getLastName(), $this->dummy_admin->getLastName());
        self::assertEquals($retrieved_admin->getJobTitle(), $this->dummy_admin->getJobTitle());
        self::assertEquals($retrieved_admin->getPhoneNo(), $this->dummy_admin->getPhoneNo());
    }

}