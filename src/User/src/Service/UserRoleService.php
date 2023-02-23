<?php

declare(strict_types=1);

namespace Frontend\User\Service;

use Doctrine\ORM\EntityRepository;
use Frontend\User\Entity\UserRole;
use Frontend\User\Repository\UserRoleRepository;
use Doctrine\ORM\EntityManager;
use Dot\AnnotatedServices\Annotation\Inject;

/**
 * Class UserRoleService
 * @package Frontend\User\Service
 */
final class UserRoleService implements UserRoleServiceInterface
{
    private readonly UserRoleRepository|EntityRepository $roleRepository;

    /**
     * UserRoleService constructor.
     *
     * @Inject({
     *     EntityManager::class
     * })
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->roleRepository = $entityManager->getRepository(UserRole::class);
    }

    public function findOneBy(array $params = []): ?UserRole
    {
        if ($params === []) {
            return null;
        }

        return $this->roleRepository->findOneBy($params);
    }
}
