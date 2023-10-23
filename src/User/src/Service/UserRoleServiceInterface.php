<?php

declare(strict_types=1);

namespace Frontend\User\Service;

use Frontend\User\Entity\UserRole;

interface UserRoleServiceInterface
{
    public function findOneBy(array $params = []): ?UserRole;
}
