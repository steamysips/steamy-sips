<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Model\User;

final class UserTest extends TestCase
{
    public function testGetSetName(): void
    {
        $user = new User();

        $all_users = $user->all();;
        var_dump($all_users);
        ob_flush();

        $this->assertCount(12, $all_users);
    }
}