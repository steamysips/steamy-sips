<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Model\User;

final class UserTest extends TestCase
{
    public function testGetSetName(): void
    {
        $user = new User();

        $user->setName("john");

        $this->assertSame($user->getName(), "john");
    }
}