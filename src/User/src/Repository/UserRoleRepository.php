<?php

declare(strict_types=1);

namespace Frontend\User\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @Entity(name="Frontend\User\Entity\UserRole")
 * @extends EntityRepository<object>
 */
class UserRoleRepository extends EntityRepository
{
}
