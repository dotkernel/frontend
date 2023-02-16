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
    /**
     * @param array $params
     * @return UserRole|null
     */
    public function findOneBy(array $params = []): ?UserRole;
}
