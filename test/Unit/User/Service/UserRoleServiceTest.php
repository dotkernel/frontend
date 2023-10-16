<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\Service;

use Frontend\User\Entity\UserRole;
use Frontend\User\Repository\UserRoleRepository;
use Frontend\User\Service\UserRoleService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class UserRoleServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testWillInstantiate(): void
    {
        $service = new UserRoleService($this->createMock(UserRoleRepository::class));

        $this->assertInstanceOf(UserRoleService::class, $service);
    }

    /**
     * @throws Exception
     */
    public function testFindOneByReturnsNull(): void
    {
        $service = new UserRoleService($this->createMock(UserRoleRepository::class));

        $this->assertNull($service->findOneBy());
    }

    /**
     * @throws Exception
     */
    public function testFindOneByReturnsUserRole(): void
    {
        $role = (new UserRole())->setName(UserRole::ROLE_USER);
        $repository = $this->createMock(UserRoleRepository::class);
        $repository->expects($this->once())->method('findOneBy')->willReturn($role);

        $service = new UserRoleService($repository);
        $result = $service->findOneBy(['name' => UserRole::ROLE_USER]);
        $this->assertSame($role->getName(), $result->getName());
    }
}