<?php

declare(strict_types=1);

namespace Frontend\App\Middleware;

use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Frontend\User\Entity\UserIdentity;
use Frontend\User\Entity\UserRole;
use Frontend\User\Repository\UserRepository;
use Frontend\User\Service\UserServiceInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\AuthenticationServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class RememberMeMiddleware
 * @package Frontend\App\Middleware
 *
 * @Service()
 */
final class RememberMeMiddleware implements MiddlewareInterface
{
    private readonly UserServiceInterface $userService;
    private readonly AuthenticationServiceInterface $authenticationService;

    /**
     * RememberMeMiddleware constructor.
     *
     * @Inject({
     *     UserServiceInterface::class,
     *     AuthenticationService::class,
     *     "config.rememberMe"
     * })
     */
    public function __construct(
        UserServiceInterface $userService,
        AuthenticationService $authenticationService
    ) {
        $this->userService = $userService;
        $this->authenticationService = $authenticationService;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function process(ServerRequestInterface $serverRequest, RequestHandlerInterface $requestHandler): ResponseInterface
    {
        $cookies = $serverRequest->getCookieParams();
        if (!empty($cookies['rememberMe'])) {
            $hash = $cookies['rememberMe'];
            $userRememberMe = $this->userService->getRepository()->getRememberUser($hash);
            if (!empty($userRememberMe)) {
                $user = $userRememberMe->getUser();
                $deviceType = $serverRequest->getServerParams()['HTTP_USER_AGENT'];
                if (
                    $hash == $userRememberMe->getRememberMeToken() && $userRememberMe->getUserAgent() == $deviceType &&
                    $userRememberMe->getExpireDate() > new DateTimeImmutable('now') && !$user->getIsDeleted()
                ) {
                    $userIdentity = new UserIdentity(
                        $user->getUuid()->toString(),
                        $user->getIdentity(),
                        $user->getRoles()->map(static function (UserRole $userRole) : string {
                            return $userRole->getName();
                        })->toArray(),
                        $user->getDetail()->getArrayCopy(),
                    );

                    /** @psalm-suppress UndefinedInterfaceMethod */
                    $this->authenticationService->getStorage()->write($userIdentity);
                } else {
                    /** @psalm-suppress UndefinedInterfaceMethod */
                    $this->authenticationService->getStorage()->clear();
                }
            }
        }

        return $requestHandler->handle($serverRequest);
    }
}
