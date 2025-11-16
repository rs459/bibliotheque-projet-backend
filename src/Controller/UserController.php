<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/users')]
class UserController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    public function deleteAccount(EntityManagerInterface $entityManager): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $userId = $user->getId();
        $userEmail = $user->getEmail();

        // Supprimer tous les livres avec DQL (ne charge pas les entités)
        $entityManager->createQuery('DELETE FROM App\Entity\Book b WHERE b.user = :userId')
            ->setParameter('userId', $userId)
            ->execute();

        // Supprimer les refresh tokens avec DQL
        $entityManager->createQuery('DELETE FROM App\Entity\RefreshToken rt WHERE rt.username = :email')
            ->setParameter('email', $userEmail)
            ->execute();

        // Supprimer l'utilisateur avec DQL pour éviter le chargement de la collection
        $entityManager->createQuery('DELETE FROM App\Entity\User u WHERE u.id = :userId')
            ->setParameter('userId', $userId)
            ->execute();

        return new JsonResponse(['message' => 'Account deleted successfully'], 200);
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
