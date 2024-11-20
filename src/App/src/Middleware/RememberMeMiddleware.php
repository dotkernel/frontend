<?php

declare(strict_types=1);

namespace Frontend\App\Middleware;

use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Dot\DependencyInjection\Attribute\Inject;
use Frontend\User\Entity\UserIdentity;
use Frontend\User\Service\UserServiceInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Exception\ExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RememberMeMiddleware implements MiddlewareInterface
{
    #[Inject(
        UserServiceInterface::class,
        AuthenticationService::class,
        "config.rememberMe",
    )]
    public function __construct(
        protected UserServiceInterface $userService,
        protected AuthenticationService $authenticationService,
        protected array $rememberConfig
    ) {
    }

    /**
     * @throws NonUniqueResultException|ExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cookies = $request->getCookieParams();
        if (! empty($cookies['rememberMe'])) {
            $hash         = $cookies['rememberMe'];
            $rememberUser = $this->userService->getRepository()->getRememberUser($hash);
            if ($rememberUser) {
                $user       = $rememberUser->getUser();
                $deviceType = $request->getServerParams()['HTTP_USER_AGENT'];
                if (
                    $hash === $rememberUser->getRememberMeToken() &&
                    $rememberUser->getUserAgent() === $deviceType &&
                    $rememberUser->getExpireDate() > new DateTimeImmutable('now') &&
                    $user->getIsDeleted() === false
                ) {
                    $userIdentity = UserIdentity::fromEntity($user);
                    $this->authenticationService->getStorage()->write($userIdentity);
                } else {
                    $this->authenticationService->getStorage()->clear();
                }
            }
        }

        return $handler->handle($request);
    }
}
