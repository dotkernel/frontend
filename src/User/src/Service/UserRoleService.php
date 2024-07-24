<?php

declare(strict_types=1);

namespace Frontend\User\Service;

use Dot\DependencyInjection\Attribute\Inject;
use Frontend\User\Entity\UserRole;
use Frontend\User\Repository\UserRoleRepository;

class UserRoleService implements UserRoleServiceInterface
{
    #[Inject(UserRoleRepository::class)]
    public function __construct(protected UserRoleRepository $roleRepository)
    {
    }

    public function findOneBy(array $params = []): ?UserRole
    {
        if (empty($params)) {
            return null;
        }

        return $this->roleRepository->findOneBy($params);
    }
}
