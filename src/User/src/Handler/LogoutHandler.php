<?php

declare(strict_types=1);

namespace Frontend\User\Handler;

use Frontend\User\Service\UserService;
use Dot\AnnotatedServices\Annotation\Inject;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class LogoutHandler
 * @package Frontend\User\Handler
 */
class LogoutHandler implements RequestHandlerInterface
{
    /** @var RouterInterface $router */
    protected $router;

    /** @var UserService $userService */
    protected $userService;

    /** @var AuthenticationServiceInterface $authenticationService */
    protected $authenticationService;

    /**
     * LogoutHandler constructor.
     * @param RouterInterface $router
     * @param UserService $userService
     * @param AuthenticationService $authenticationService
     *
     * @Inject({RouterInterface::class, AuthenticationService::class, UserService::class})
     */
    public function __construct(
        RouterInterface $router,
        AuthenticationService $authenticationService,
        UserService $userService
    ) {
        $this->router = $router;
        $this->authenticationService = $authenticationService;
        $this->userService = $userService;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->authenticationService->clearIdentity();
        return new RedirectResponse(
            $this->router->generateUri('page.home')
        );
    }
}
