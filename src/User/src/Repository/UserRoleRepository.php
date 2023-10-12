<?php

declare(strict_types=1);

namespace Frontend\User\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Frontend\User\Entity\UserRole;
use Dot\AnnotatedServices\Annotation\Entity;

/**
 * @Entity(name="Frontend\User\Entity\UserRole")
 * @extends EntityRepository<object>
 */
class UserRoleRepository extends EntityRepository
{
    /**
     * @param string $name
     * @return UserRole|null
     * @throws NonUniqueResultException
     */
    public function findByName(string $name): ?UserRole
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('role')
            ->from(UserRole::class, 'role')
            ->andWhere('role.name = :name')
            ->setParameter('name', $name);

        return $qb->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }
}
