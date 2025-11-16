<?php

namespace App\DTO;

class GoogleBookDto
{
    private ?string $googleId = null;
    private ?string $title = null;
    private ?string $description = null;
    private ?int $pageCount = null;
    private ?string $thumbnail = null;
    private array $authors = [];
    private ?string $publisher = null;
    private ?string $publishedDate = null;
    private ?string $isbn10 = null;
    private ?string $isbn13 = null;

    public static function fromApiResponse(array $item): self
    {
        $dto = new self();

        $volumeInfo = $item['volumeInfo'] ?? [];

        $dto->googleId = $item['id'] ?? null;
        $dto->title = $volumeInfo['title'] ?? 'Sans titre';
        $dto->description = $volumeInfo['description'] ?? 'Aucune description disponible';
        $dto->pageCount = $volumeInfo['pageCount'] ?? 0;
        $dto->authors = $volumeInfo['authors'] ?? [];
        $dto->publisher = $volumeInfo['publisher'] ?? null;
        $dto->publishedDate = $volumeInfo['publishedDate'] ?? null;

        // Récupération de la miniature
        $imageLinks = $volumeInfo['imageLinks'] ?? [];
        $dto->thumbnail = $imageLinks['thumbnail'] ?? $imageLinks['smallThumbnail'] ?? 'https://via.placeholder.com/150';

        // Récupération des ISBN
        $industryIdentifiers = $volumeInfo['industryIdentifiers'] ?? [];
        foreach ($industryIdentifiers as $identifier) {
            if ($identifier['type'] === 'ISBN_10') {
                $dto->isbn10 = $identifier['identifier'];
            } elseif ($identifier['type'] === 'ISBN_13') {
                $dto->isbn13 = $identifier['identifier'];
            }
        }

        return $dto;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPageCount(): ?int
    {
        return $this->pageCount;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function getAuthors(): array
    {
        return $this->authors;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function getPublishedDate(): ?string
    {
        return $this->publishedDate;
    }

    public function getIsbn10(): ?string
    {
        return $this->isbn10;
    }

    public function getIsbn13(): ?string
    {
        return $this->isbn13;
    }

    public function toArray(): array
    {
        return [
            'googleId' => $this->googleId,
            'title' => $this->title,
            'description' => $this->description,
            'pageCount' => $this->pageCount,
            'thumbnail' => $this->thumbnail,
            'authors' => $this->authors,
            'publisher' => $this->publisher,
            'publishedDate' => $this->publishedDate,
            'isbn10' => $this->isbn10,
            'isbn13' => $this->isbn13,
        ];
    }
}
