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
    protected UserRoleRepository $roleRepository;

    /**
     * @Inject({
     *     UserRoleRepository::class,
     * })
     */
    public function __construct(UserRoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function findOneBy(array $params = []): ?UserRole
    {
        if (empty($params)) {
            return null;
        }

        return $this->roleRepository->findOneBy($params);
    }
}
