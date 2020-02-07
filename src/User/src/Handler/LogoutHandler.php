<?php

declare(strict_types=1);

namespace Frontend\User\Handler;

use Dot\AnnotatedServices\Annotation\Inject;
use Frontend\User\Service\UserService;
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

    /**
     * LogoutHandler constructor.
     * @param RouterInterface $router
     * @param UserService $userService
     *
     * @Inject({RouterInterface::class, UserService::class})
     */
    public function __construct(
        RouterInterface $router,
        UserService $userService
    ) {
        $this->router = $router;
        $this->userService = $userService;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        exit('ok');
        return new RedirectResponse(
            $this->router->generateUri('page.home')
        );
    }
}
