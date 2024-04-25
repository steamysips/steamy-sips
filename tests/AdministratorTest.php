<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Model\Administrator;
use Steamy\Core\Database;

final class AdministratorTest extends TestCase
{
    use Database;
    private ?Administrator $dummy_admin;

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
    
        // Clear all data from administrator and user tables
        self::query('DELETE FROM administrator; DELETE FROM user;');
    }
    
    public function testConstructor(): void
    {
        // check if fields were correctly set
        self::assertEquals(-1, $this->dummy_admin->getUserID());
        self::assertEquals("john@gmail.com", $this->dummy_admin->getEmail());
        self::assertEquals("john", $this->dummy_admin->getFirstName());
        self::assertEquals("prince", $this->dummy_admin->getLastName());
        self::assertEquals("13213431", $this->dummy_admin->getPhoneNo());
        self::assertFalse($this->dummy_admin->isSuperAdmin());
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
        self::assertEquals(-1, $result['user_id']);
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
            'last_name' => 'Last name must be at least 3 characters long', // Include last_name error message
            'job_title' => 'Job title is required',
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
    
    public function testVerifyPassword()
    {
        // verify true password
        $this->assertTrue($this->dummy_admin->verifyPassword("abcd"));

        // reject empty string
        $this->assertNotTrue($this->dummy_admin->verifyPassword(""));

        // reject any other string
        $this->assertNotTrue($this->dummy_admin->verifyPassword("abcde"));
        $this->assertNotTrue($this->dummy_admin->verifyPassword("abcd "));
        $this->assertNotTrue($this->dummy_admin->verifyPassword(" abcd"));
    }

    public function testGetByEmail(): void
    {
        // Create an administrator object with correct parameters
        $admin = new Administrator(
            "admin@example.com", "Admin", "User", "AdminPass",
            "123456789", "Administrator", true
        );
    
        // Get the administrator by email
        $retrieved_admin = Administrator::getByEmail("john@gmail.com");
    
        // Assert that the retrieved administrator is not null
        $this->assertNotNull($retrieved_admin);
    }
    
}