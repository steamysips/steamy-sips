<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Model\Administrator;

final class AdministratorTest extends TestCase
{
    private ?Administrator $dummy_admin;

    public function setUp(): void
    {
        $this->dummy_admin = new Administrator(
            "john@gmail.com", "john", "prince", "abcd",
            "13213431", "Manager", false
        );
    }

    public function tearDown(): void
    {
        $this->dummy_admin = null;
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

    public function testValidate()
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
            'job_title' => 'Job title is required',
            'job_title' => 'Job title must be longer than 3 characters'

        ],
            $administrator->validate());

        $this->markTestIncomplete(
            'This test lacks range checks, ...',
        );
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

    public function testGetByEmail()
    {
        $this->markTestIncomplete(
            'TODO',
        );
    }
}