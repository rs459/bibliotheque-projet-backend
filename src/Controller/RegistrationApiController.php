<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RegistrationApiController extends AbstractController
{
    #[Route('/api/register', name: 'app_api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): Response {
        $data = $request->toArray();

        $user = new User();
        $user->setEmail($data['email'] ?? '');
        $user->setRoles(['ROLE_USER']);

        // Hachage du mot de passe avant de le définir
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password'] ?? '');
        $user->setPassword($hashedPassword);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'User registration successful'], Response::HTTP_CREATED);
    }
}
