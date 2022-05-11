<?php
// api/src/Doctrine/CurrentUserExtension.php

namespace App\Doctrine;

use App\Entity\User;
use App\Entity\Offer;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;

final class CurrentUserExtension implements QueryCollectionExtensionInterface
{
  private $security;

  public function __construct(Security $security)
  {
    $this->security = $security;
  }

  public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
  {
    $this->addWhere($queryBuilder, $resourceClass);
  }



  private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
  {
    /** @var User $user */
    if (User::class !== $resourceClass || $this->security->isGranted('ROLE_ADMIN') || null === $user = $this->security->getUser()) {
      return;
    }

    $rootAlias = $queryBuilder->getRootAliases()[0];
    $queryBuilder->andWhere(sprintf('%s.id = :current_user', $rootAlias));
    $queryBuilder->setParameter('current_user', $user->getId());
  }
}
