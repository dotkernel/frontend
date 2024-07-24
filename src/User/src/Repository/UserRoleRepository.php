<?php

declare(strict_types=1);

namespace Frontend\User\Repository;

use Doctrine\ORM\EntityRepository;
use Dot\DependencyInjection\Attribute\Entity;
use Frontend\User\Entity\UserRole;

/**
 * @extends EntityRepository<object>
 */
#[Entity(name: UserRole::class)]
class UserRoleRepository extends EntityRepository
{
}
