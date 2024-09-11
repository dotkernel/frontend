<?php

declare(strict_types=1);

namespace Frontend\User\Repository;

use Doctrine\ORM\EntityRepository;
use Dot\DependencyInjection\Attribute\Entity;
use Frontend\User\Entity\UserAvatar;
use Ramsey\Uuid\Uuid;

/**
 * @extends EntityRepository<object>
 */
#[Entity(name: UserAvatar::class)]
class UserAvatarRepository extends EntityRepository
{
    public function deleteAvatar(string $uuid): mixed
    {
        $uuid = Uuid::fromString($uuid)->getBytes();
        $qb   = $this->getEntityManager()->createQueryBuilder();
        $qb->delete(UserAvatar::class, 'user_avatar')
            ->where('user_avatar.uuid = :uuid')
            ->setParameter('uuid', $uuid);

        return $qb->getQuery()->useQueryCache(true)->execute();
    }
}
