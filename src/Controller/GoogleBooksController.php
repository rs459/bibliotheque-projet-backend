<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Editor;
use App\Service\GoogleBooksApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class GoogleBooksController extends AbstractController
{
    public function __construct(
        private GoogleBooksApiService $googleBooksService,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Recherche des livres sur Google Books API
     */
    #[Route('/google-books/search', name: 'google_books_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q');

        if (!$query) {
            return $this->json([
                'error' => 'Le paramètre "q" est requis'
            ], 400);
        }

        try {
            $maxResults = $request->query->getInt('maxResults', 10);
            $books = $this->googleBooksService->searchBooks($query, $maxResults);

            return $this->json([
                'success' => true,
                'count' => count($books),
                'items' => array_map(fn ($book) => $book->toArray(), $books)
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Importe un livre depuis Google Books et l'enregistre en base de données
     */
    #[Route('/google-books/import', name: 'google_books_import', methods: ['POST'])]
    public function import(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['googleId'])) {
            return $this->json([
                'error' => 'Le champ "googleId" est requis'
            ], 400);
        }

        try {
            // Récupère les détails du livre depuis Google Books
            $googleBook = $this->googleBooksService->getBookById($data['googleId']);

            // Gestion de l'auteur
            $author = $this->findOrCreateAuthor($googleBook->getAuthors());

            // Gestion de l'éditeur
            $editor = $this->findOrCreateEditor($googleBook->getPublisher());

            // Récupérer l'utilisateur connecté
            $user = $this->getUser();
            if (!$user) {
                return $this->json([
                    'error' => 'Vous devez être connecté pour importer un livre'
                ], 401);
            }

            // Création du livre
            $book = new Book();
            $book->setTitle($googleBook->getTitle());
            $book->setDescription($googleBook->getDescription());
            $book->setPages($googleBook->getPageCount() ?? 0);
            $book->setImage($googleBook->getThumbnail());
            $book->setAuthor($author);
            $book->setEditor($editor);
            $book->setUser($user);

            $this->entityManager->persist($book);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Livre importé avec succès',
                'book' => [
                    'id' => $book->getId(),
                    'title' => $book->getTitle(),
                    'author' => [
                        'id' => $author->getId(),
                        'firstName' => $author->getFirstName(),
                        'lastName' => $author->getLastName(),
                    ],
                    'editor' => [
                        'id' => $editor->getId(),
                        'name' => $editor->getName(),
                    ]
                ]
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Erreur lors de l\'importation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Trouve ou crée un auteur basé sur le nom complet
     */
    private function findOrCreateAuthor(array $authors): Author
    {
        // Si aucun auteur n'est fourni, crée un auteur "Inconnu"
        if (empty($authors)) {
            $authorName = 'Auteur Inconnu';
        } else {
            // Prend le premier auteur de la liste
            $authorName = $authors[0];
        }

        // Sépare le prénom et le nom (basique)
        $nameParts = explode(' ', $authorName, 2);
        $firstName = $nameParts[0] ?? 'Inconnu';
        $lastName = $nameParts[1] ?? '';

        // Recherche si l'auteur existe déjà
        $authorRepo = $this->entityManager->getRepository(Author::class);
        $author = $authorRepo->findOneBy([
            'firstName' => $firstName,
            'lastName' => $lastName
        ]);

        // Si l'auteur n'existe pas, on le crée
        if (!$author) {
            $author = new Author();
            $author->setFirstName($firstName);
            $author->setLastName($lastName);

            $this->entityManager->persist($author);
        }

        return $author;
    }

    /**
     * Trouve ou crée un éditeur basé sur le nom
     */
    private function findOrCreateEditor(?string $publisherName): Editor
    {
        // Si aucun éditeur n'est fourni, crée un éditeur "Inconnu"
        $name = $publisherName ?? 'Éditeur Inconnu';

        // Recherche si l'éditeur existe déjà
        $editorRepo = $this->entityManager->getRepository(Editor::class);
        $editor = $editorRepo->findOneBy(['name' => $name]);

        // Si l'éditeur n'existe pas, on le crée
        if (!$editor) {
            $editor = new Editor();
            $editor->setName($name);

            $this->entityManager->persist($editor);
        }

        return $editor;
    }
}
