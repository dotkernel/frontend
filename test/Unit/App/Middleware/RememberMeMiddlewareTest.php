<?php

declare(strict_types=1);

namespace FrontendTest\Unit\App\Middleware;

use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Frontend\App\Middleware\RememberMeMiddleware;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserDetail;
use Frontend\User\Entity\UserIdentity;
use Frontend\User\Entity\UserRememberMe;
use Frontend\User\Entity\UserRole;
use Frontend\User\Repository\UserRepository;
use Frontend\User\Service\UserService;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Exception\ExceptionInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RememberMeMiddlewareTest extends TestCase
{
    private ServerRequestInterface $request;

    private RequestHandlerInterface $handler;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->handler = $this->createMock(RequestHandlerInterface::class);
    }

    /**
     * @throws Exception
     */
    public function testWillCreate(): void
    {
        $middleware = new RememberMeMiddleware(
            $this->createMock(UserService::class),
            $this->createMock(AuthenticationService::class),
            [],
        );

        $this->assertInstanceOf(RememberMeMiddleware::class, $middleware);
    }

    /**
     * @throws ExceptionInterface
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function testAutologin(): void
    {
        $userAgent             = 'google';
        $token                 = 'token';
        $userService           = $this->createMock(UserService::class);
        $userRepository        = $this->createMock(UserRepository::class);
        $authenticationService = new AuthenticationService();

        $user = (new User())
            ->setIdentity('test@dotkernel.com')
            ->setIsDeleted(false)
            ->activate();

        $detail = (new UserDetail())
            ->setUser($user)
            ->setFirstName('test')
            ->setLastName('test');

        $user->setDetail($detail);

        $userIdentity = new UserIdentity(
            $user->getUuid()->toString(),
            $user->getIdentity(),
            $user->getRoles()->map(function (UserRole $userRole) {
                return $userRole->getName();
            })->toArray(),
            $user->getDetail()->getArrayCopy(),
        );

        $userRememberMe = (new UserRememberMe())
            ->setUser($user)
            ->setUserAgent($userAgent)
            ->setRememberMeToken($token)
            ->setExpireDate((new DateTimeImmutable())->add(new DateInterval('P1D')));

        $userRepository->expects($this->once())->method('getRememberUser')->willReturn($userRememberMe);
        $userService->expects($this->once())->method('getRepository')->willReturn($userRepository);

        $this->request->expects($this->once())->method('getCookieParams')->willReturn([
            'rememberMe' => $token,
        ]);

        $this->request->expects($this->once())->method('getServerParams')->willReturn([
            'HTTP_USER_AGENT' => $userAgent,
        ]);

        $middleware = new RememberMeMiddleware(
            $userService,
            $authenticationService,
            [],
        );

        $middleware->process($this->request, $this->handler);

        $identity = $authenticationService->getIdentity();

        $this->assertSame($userIdentity->getUuid(), $identity->getUuid());
        $this->assertSame($userIdentity->getIdentity(), $identity->getIdentity());
    }

    /**
     * @throws ExceptionInterface
     * @throws Exception
     * @throws NonUniqueResultException
     */
    public function testAutologinExpired(): void
    {
        $userAgent             = 'google';
        $token                 = 'token';
        $userService           = $this->createMock(UserService::class);
        $userRepository        = $this->createMock(UserRepository::class);
        $authenticationService = new AuthenticationService();

        $user = (new User())
            ->setIdentity('test@dotkernel.com')
            ->setIsDeleted(false)
            ->activate();

        $detail = (new UserDetail())
            ->setUser($user)
            ->setFirstName('test')
            ->setLastName('test');

        $user->setDetail($detail);

        $userRememberMe = (new UserRememberMe())
            ->setUser($user)
            ->setUserAgent($userAgent)
            ->setRememberMeToken($token)
            ->setExpireDate((new DateTimeImmutable())->sub(new DateInterval('P1D')));

        $userRepository->expects($this->once())->method('getRememberUser')->willReturn($userRememberMe);
        $userService->expects($this->once())->method('getRepository')->willReturn($userRepository);

        $this->request->expects($this->once())->method('getCookieParams')->willReturn([
            'rememberMe' => $token,
        ]);

        $this->request->expects($this->once())->method('getServerParams')->willReturn([
            'HTTP_USER_AGENT' => $userAgent,
        ]);

        $middleware = new RememberMeMiddleware(
            $userService,
            $authenticationService,
            [],
        );

        $middleware->process($this->request, $this->handler);

        $this->assertTrue($authenticationService->getStorage()->isEmpty());
    }
}
