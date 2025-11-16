<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserControllerTest extends WebTestCase
{
    private function createAuthenticatedClient(string $email = 'test@test.com', array $roles = ['ROLE_USER']): \Symfony\Bundle\FrameworkBundle\KernelBrowser
    {
        $client = static::createClient();
        $container = static::getContainer();

        $em = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        // Create a test user
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $hashedPassword = $passwordHasher->hashPassword($user, 'Test123!');
        $user->setPassword($hashedPassword);

        $em->persist($user);
        $em->flush();

        // Login
        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => $email,
            'password' => 'Test123!',
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);
        $token = $data['token'];

        $client->setServerParameter('HTTP_AUTHORIZATION', sprintf('Bearer %s', $token));

        return $client;
    }

    public function testBlockUserAsAdmin(): void
    {
        $client = $this->createAuthenticatedClient('admin@test.com', ['ROLE_ADMIN']);
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        // Create a user to block
        $userToBlock = new User();
        $userToBlock->setEmail('blockme@test.com');
        $userToBlock->setPassword('hashed');
        $userToBlock->setRoles(['ROLE_USER']);
        $em->persist($userToBlock);
        $em->flush();

        $userId = $userToBlock->getId();

        // Block the user
        $client->request('PATCH', "/api/users/{$userId}/block");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $data);
        $this->assertTrue($data['user']['isBlocked']);

        // Cleanup
        $em->remove($userToBlock);
        $em->flush();
    }

    public function testUnblockUserAsAdmin(): void
    {
        $client = $this->createAuthenticatedClient('admin2@test.com', ['ROLE_ADMIN']);
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        // Create a blocked user
        $blockedUser = new User();
        $blockedUser->setEmail('blocked@test.com');
        $blockedUser->setPassword('hashed');
        $blockedUser->setRoles(['ROLE_USER']);
        $blockedUser->setIsBlocked(true);
        $em->persist($blockedUser);
        $em->flush();

        $userId = $blockedUser->getId();

        // Unblock the user
        $client->request('PATCH', "/api/users/{$userId}/unblock");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse($data['user']['isBlocked']);

        // Cleanup
        $em->remove($blockedUser);
        $em->flush();
    }

    public function testCannotBlockAdminUser(): void
    {
        $client = $this->createAuthenticatedClient('admin3@test.com', ['ROLE_ADMIN']);
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        // Create an admin user
        $adminUser = new User();
        $adminUser->setEmail('anotheradmin@test.com');
        $adminUser->setPassword('hashed');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $em->persist($adminUser);
        $em->flush();

        $userId = $adminUser->getId();

        // Try to block the admin user
        $client->request('PATCH', "/api/users/{$userId}/block");

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        // Cleanup
        $em->remove($adminUser);
        $em->flush();
    }

    public function testNonAdminCannotBlockUser(): void
    {
        $client = $this->createAuthenticatedClient('regular@test.com', ['ROLE_USER']);

        // Try to block a user without admin rights
        $client->request('PATCH', '/api/users/999/block');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
