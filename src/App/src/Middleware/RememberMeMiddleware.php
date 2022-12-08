<?php

declare(strict_types=1);

namespace Frontend\App\Middleware;

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
class RememberMeMiddleware implements MiddlewareInterface
{
    /** @var  UserServiceInterface */
    protected $userService;

    /** @var AuthenticationServiceInterface $authenticationService */
    protected AuthenticationServiceInterface $authenticationService;

    /** @var UserRepository $repository */
    protected $repository;

    /** @var array */
    protected $rememberConfig;

    /**
     * TranslatorMiddleware constructor.
     * @param UserServiceInterface $userService
     * @param AuthenticationService $authenticationService
     * @param array $rememberConfig
     *
     * @Inject({UserServiceInterface::class, AuthenticationService::class, "config.rememberMe"})
     */
    public function __construct(
        UserServiceInterface $userService,
        AuthenticationService $authenticationService,
        array $rememberConfig
    ) {
        $this->userService = $userService;
        $this->authenticationService = $authenticationService;
        $this->rememberConfig = $rememberConfig;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!empty($_COOKIE['rememberMe'])) {
            $hash = $_COOKIE['rememberMe'];
            $rememberUser = $this->userService->getRepository()->getRememberUser($hash);
            if (!empty($rememberUser)) {
                $user = $rememberUser->getUser();
                $deviceType = $request->getServerParams()['HTTP_USER_AGENT'];
                if (
                    $hash == $rememberUser->getRememberMeToken() && $rememberUser->getUserAgent() == $deviceType &&
                    $rememberUser->getExpireDate() > new \DateTimeImmutable('now') && $user->getIsDeleted() === false
                ) {
                    $userIdentity = new UserIdentity(
                        $user->getUuid()->toString(),
                        $user->getIdentity(),
                        $user->getRoles()->map(function (UserRole $userRole) {
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

        return $handler->handle($request);
    }
}
