<?php

declare(strict_types=1);

namespace Frontend\User\Service;

use Frontend\User\Entity\UserRole;

/**
 * Interface UserRoleService
 */
interface UserRoleServiceInterface
{
    /**
     * @param array $params
     */
    public function findOneBy(array $params = []): ?UserRole;
}
