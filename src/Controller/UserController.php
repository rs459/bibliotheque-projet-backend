<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/users')]
class UserController extends AbstractController
{
    #[Route('/me', name: 'api_user_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deleteAccount(EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(['message' => 'Account deleted successfully']);
    }

    #[Route('/{id}/block', name: 'api_user_block', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function blockUser(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Cannot block an admin account'], 403);
        }

        $user->setIsBlocked(true);
        $entityManager->flush();

        return $this->json([
            'message' => 'User blocked successfully',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'isBlocked' => $user->isBlocked()
            ]
        ]);
    }

    #[Route('/{id}/unblock', name: 'api_user_unblock', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function unblockUser(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $user->setIsBlocked(false);
        $entityManager->flush();

        return $this->json([
            'message' => 'User unblocked successfully',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'isBlocked' => $user->isBlocked()
            ]
        ]);
    }
}
