<?php

declare(strict_types=1);

namespace Frontend\User\Service;

use Frontend\User\Entity\UserRole;

/**
 * Interface UserRoleService
 * @package Frontend\User\Service
 */
interface UserRoleServiceInterface
{
    public function findOneBy(array $params = []): ?UserRole;
}
