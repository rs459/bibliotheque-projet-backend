<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cette adresse e-mail n\'est pas disponible.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'L\'adresse e-mail ne peut pas être vide.')]
    #[Assert\Email(message: 'L\'adresse e-mail "{{ value }}" n\'est pas une adresse e-mail valide.')]
    #[Assert\Length(max: 180, maxMessage: 'L\'adresse e-mail ne peut pas dépasser {{ limit }} caractères.')]

    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le mot de passe ne peut pas être vide.')]
    #[Assert\Length(min: 6, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.')]
    #[Assert\Regex(
        pattern: '/[A-Z]/',
        message: 'Le mot de passe doit contenir au moins une lettre majuscule.'
    )]
    #[Assert\Regex(
        pattern: '/[a-z]/',
        message: 'Le mot de passe doit contenir au moins une lettre minuscule.'
    )]
    #[Assert\Regex(
        pattern: '/\d/',
        message: 'Le mot de passe doit contenir au moins un chiffre.'
    )]
    #[Assert\Regex(
        pattern: '/[^a-zA-Z0-9]/',
        message: 'Le mot de passe doit contenir au moins un caractère spécial.'
    )]

    private ?string $password = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isBlocked = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: RefreshToken::class, cascade: ['persist', 'remove'])]
    private Collection $refreshTokens;

    /**
     * @var Collection<int, Book>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Book::class, cascade: ['persist', 'remove'])]
    private Collection $books;

    public function __construct()
    {
        $this->refreshTokens = new ArrayCollection();
        $this->books = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $this;
    }

    public function eraseCredentials(): void
    {
        // Nothing to do here
    }

    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): static
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    public function getRefreshTokens(): Collection
    {
        return $this->refreshTokens;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setUser($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getUser() === $this) {
                $book->setUser(null);
            }
        }

        return $this;
    }
}
