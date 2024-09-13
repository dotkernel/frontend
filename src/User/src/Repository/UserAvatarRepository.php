<?php

declare(strict_types=1);

namespace Frontend\User\Repository;

use Doctrine\ORM\EntityRepository;
use Dot\DependencyInjection\Attribute\Entity;
use Frontend\User\Entity\UserAvatar;

/**
 * @extends EntityRepository<object>
 */
#[Entity(name: UserAvatar::class)]
class UserAvatarRepository extends EntityRepository
{
    public function deleteAvatar(UserAvatar $avatar): void
    {
        $this->getEntityManager()->remove($avatar);
        $this->getEntityManager()->flush();
    }
}
