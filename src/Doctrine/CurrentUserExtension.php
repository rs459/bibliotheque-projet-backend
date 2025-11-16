<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Editor;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $user = $this->security->getUser();

        // Ne pas filtrer pour les admins ou si pas d'utilisateur connecté
        if ($this->security->isGranted('ROLE_ADMIN') || null === $user) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        if (Book::class === $resourceClass) {
            // Filtrer les livres par utilisateur
            $queryBuilder->andWhere(sprintf('%s.user = :current_user', $rootAlias));
            $queryBuilder->setParameter('current_user', $user);
        } elseif (Author::class === $resourceClass) {
            // Ne montrer que les auteurs qui ont au moins un livre de l'utilisateur
            $queryBuilder->innerJoin(sprintf('%s.books', $rootAlias), 'book');
            $queryBuilder->andWhere('book.user = :current_user');
            $queryBuilder->setParameter('current_user', $user);
        } elseif (Editor::class === $resourceClass) {
            // Ne montrer que les éditeurs qui ont au moins un livre de l'utilisateur
            $queryBuilder->innerJoin(sprintf('%s.books', $rootAlias), 'book');
            $queryBuilder->andWhere('book.user = :current_user');
            $queryBuilder->setParameter('current_user', $user);
        }
    }
}
