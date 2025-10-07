<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

        // 1. Valider les données brutes avant de créer l'utilisateur
        $constraints = new Assert\Collection([
            'email' => [
                new Assert\NotBlank(['message' => 'L\'adresse e-mail ne peut pas être vide.']),
                new Assert\Email(['message' => 'L\'adresse e-mail "{{ value }}" n\'est pas une adresse e-mail valide.']),
            ],
            'password' => [
                new Assert\NotBlank(['message' => 'Le mot de passe ne peut pas être vide.']),
                new Assert\Length(['min' => 6, 'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.']),
                new Assert\Regex(['pattern' => '/[A-Z]/', 'message' => 'Le mot de passe doit contenir au moins une lettre majuscule.']),
                new Assert\Regex(['pattern' => '/[a-z]/', 'message' => 'Le mot de passe doit contenir au moins une lettre minuscule.']),
                new Assert\Regex(['pattern' => '/\d/', 'message' => 'Le mot de passe doit contenir au moins un chiffre.']),
                new Assert\Regex(['pattern' => '/[^a-zA-Z0-9]/', 'message' => 'Le mot de passe doit contenir au moins un caractère spécial.']),
            ],
        ]);

        $violations = $validator->validate($data, $constraints);

        if (count($violations) > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        // 2. Créer l'utilisateur si les données sont valides
        $user = new User();
        $user->setEmail($data['email']);
        $user->setRoles(['ROLE_USER']);

        // 3. Hacher le mot de passe maintenant qu'il est validé
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'User registration successful'], Response::HTTP_CREATED); // 201
    }

    #[Route('/api/users', name: 'app_api_users_get', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getUsers(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();

        $usersArray = [];
        foreach ($users as $user) {
            $usersArray[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ];
        }

        return $this->json($usersArray, Response::HTTP_OK);
    }
}
