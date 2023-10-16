<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\Service;

use Dot\Mail\Service\MailService;
use Frontend\App\Common\Message;
use Frontend\App\Service\CookieServiceInterface;
use Frontend\User\Entity\User;
use Frontend\User\Repository\UserRepository;
use Frontend\User\Repository\UserRoleRepository;
use Frontend\User\Service\UserRoleServiceInterface;
use Frontend\User\Service\UserService;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception as PHPUnitException;
use PHPUnit\Framework\TestCase;
use Exception;

class UserServiceTest extends TestCase
{
    /**
     * @throws PHPUnitException
     */
    public function testWillInstantiate(): void
    {
        $service = new UserService(
            $this->createMock(CookieServiceInterface::class),
            $this->createMock(MailService::class),
            $this->createMock(UserRoleServiceInterface::class),
            $this->createMock(TemplateRendererInterface::class),
            $this->createMock(UserRepository::class),
            $this->createMock(UserRoleRepository::class),
            []
        );

        $this->assertInstanceOf(UserService::class, $service);
    }

    /**
     * @throws PHPUnitException
     */
    public function testCreateUserThrowsDuplicateException(): void
    {
        $cookieService = $this->createMock(CookieServiceInterface::class);
        $mailService = $this->createMock(MailService::class);
        $userRoleService = $this->createMock(UserRoleServiceInterface::class);
        $template = $this->createMock(TemplateRendererInterface::class);
        $userRepository = $this->createMock(UserRepository::class);
        $userRoleRepository = $this->createMock(UserRoleRepository::class);

        $userRepository->expects($this->once())->method('exists')->willReturn((new User()));
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(Message::DUPLICATE_EMAIL);

        $service = new UserService(
            $cookieService,
            $mailService,
            $userRoleService,
            $template,
            $userRepository,
            $userRoleRepository,
            []
        );

        $service->createUser(['email' => 'test@dotkernel.com']);
    }

    /**
     * @throws PHPUnitException
     */
    public function testCreateUserThrowsRestrictionRolesException(): void
    {
        $cookieService = $this->createMock(CookieServiceInterface::class);
        $mailService = $this->createMock(MailService::class);
        $userRoleService = $this->createMock(UserRoleServiceInterface::class);
        $template = $this->createMock(TemplateRendererInterface::class);
        $userRepository = $this->createMock(UserRepository::class);
        $userRoleRepository = $this->createMock(UserRoleRepository::class);

        $userRepository->expects($this->once())->method('exists')->willReturn(null);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(Message::RESTRICTION_ROLES);

        $service = new UserService(
            $cookieService,
            $mailService,
            $userRoleService,
            $template,
            $userRepository,
            $userRoleRepository,
            []
        );

        $service->createUser([
            'email' => 'test@dotkernel.com',
            'identity' => 'test',
            'password' => 'password',
            'detail' => [
                'firstName' => 'Test',
                'lastName' => 'Dot Kernel',
            ]
        ]);
    }
}