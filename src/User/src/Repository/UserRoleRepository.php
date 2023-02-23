<?php

declare(strict_types=1);

namespace Frontend\User\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Frontend\User\Entity\UserRole;

/**
 * Class UserRoleRepository
 * @package Frontend\User\Repository
 */
final class UserRoleRepository extends EntityRepository
{
    /**
     * @throws NonUniqueResultException
     */
    public function findByName(string $name): ?UserRole
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('role')
            ->from(UserRole::class, 'role')
            ->andWhere('role.name = :name')
            ->setParameter('name', $name);

        return $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }
}
