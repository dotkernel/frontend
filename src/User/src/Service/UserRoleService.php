<?php

declare(strict_types=1);

namespace Frontend\User\Service;

use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Frontend\User\Entity\UserRole;
use Frontend\User\Repository\UserRoleRepository;

/**
 * @Service()
 */
class UserRoleService implements UserRoleServiceInterface
{
    /**
     * @Inject({
     *     UserRoleRepository::class,
     * })
     */
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
