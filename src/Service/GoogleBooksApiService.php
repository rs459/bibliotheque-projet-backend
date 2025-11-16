<?php

namespace App\Service;

use App\DTO\GoogleBookDto;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class GoogleBooksApiService
{
    private const GOOGLE_BOOKS_API_URL = 'https://www.googleapis.com/books/v1/volumes';

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $googleBooksApiKey = ''
    ) {
    }

    /**
     * Recherche des livres par titre, auteur ou ISBN
     *
     * @param string $query Requête de recherche
     * @param int $maxResults Nombre maximum de résultats (default: 10)
     * @return array Liste de GoogleBookDto
     */
    public function searchBooks(string $query, int $maxResults = 10): array
    {
        try {
            $queryParams = [
                'q' => $query,
                'maxResults' => $maxResults,
            ];

            // Ajouter la clé API seulement si elle est configurée
            if (!empty($this->googleBooksApiKey)) {
                $queryParams['key'] = $this->googleBooksApiKey;
            }

            $response = $this->httpClient->request('GET', self::GOOGLE_BOOKS_API_URL, [
                'query' => $queryParams
            ]);

            $data = $response->toArray();

            if (!isset($data['items']) || empty($data['items'])) {
                return [];
            }

            return array_map(
                fn ($item) => GoogleBookDto::fromApiResponse($item),
                $data['items']
            );
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la recherche Google Books', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);
            throw new \RuntimeException('Impossible de rechercher des livres: ' . $e->getMessage());
        }
    }

    /**
     * Récupère les détails d'un livre par son ID Google
     *
     * @param string $googleId ID du livre sur Google Books
     * @return GoogleBookDto
     */
    public function getBookById(string $googleId): GoogleBookDto
    {
        try {
            $queryParams = [];

            // Ajouter la clé API seulement si elle est configurée
            if (!empty($this->googleBooksApiKey)) {
                $queryParams['key'] = $this->googleBooksApiKey;
            }

            $response = $this->httpClient->request('GET', self::GOOGLE_BOOKS_API_URL . '/' . $googleId, [
                'query' => $queryParams
            ]);
            $data = $response->toArray();

            return GoogleBookDto::fromApiResponse($data);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la récupération du livre Google Books', [
                'googleId' => $googleId,
                'error' => $e->getMessage()
            ]);
            throw new \RuntimeException('Impossible de récupérer le livre: ' . $e->getMessage());
        }
    }

    /**
     * Recherche des livres par ISBN
     *
     * @param string $isbn ISBN du livre
     * @return GoogleBookDto|null
     */
    public function searchByIsbn(string $isbn): ?GoogleBookDto
    {
        $results = $this->searchBooks('isbn:' . $isbn, 1);
        return $results[0] ?? null;
    }
}
