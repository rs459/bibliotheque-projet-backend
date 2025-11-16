<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserCreation(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('hashedPassword123');
        $user->setRoles(['ROLE_USER']);

        $this->assertSame('test@example.com', $user->getEmail());
        $this->assertSame('hashedPassword123', $user->getPassword());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('user@test.com');

        $this->assertSame('user@test.com', $user->getUserIdentifier());
    }

    public function testUserDefaultRoles(): void
    {
        $user = new User();
        $user->setRoles([]);

        // Should always contain at least ROLE_USER
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testUserBlockedStatus(): void
    {
        $user = new User();

        // Default should be not blocked
        $this->assertFalse($user->isBlocked());

        $user->setIsBlocked(true);
        $this->assertTrue($user->isBlocked());

        $user->setIsBlocked(false);
        $this->assertFalse($user->isBlocked());
    }

    public function testAdminRole(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles()); // Should always have ROLE_USER
    }
}
