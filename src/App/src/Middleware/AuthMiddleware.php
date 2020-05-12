<?php

declare(strict_types=1);

namespace Frontend\App\Middleware;

use Frontend\User\Entity\User;
use Frontend\User\Entity\UserInterface;
use Frontend\User\Entity\UserRole;
use Frontend\User\Service\UserServiceInterface;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\FlashMessenger\FlashMessenger;
use Laminas\Authentication\AuthenticationService;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Http\Response;
use Mezzio\Authorization\AuthorizationInterface;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class AuthTeamMiddleware
 * @package App\User\Middleware
 *
 */
class AuthMiddleware implements MiddlewareInterface
{
    /** @var AuthenticationService $authenticationService */
    protected $authenticationService;

    /** @var AuthorizationInterface $authorization */
    protected $authorization;

    /** @var array $authorizationConfig */
    protected $authorizationConfig;

    /** @var RouterInterface $router */
    protected $router;

    /** @var FlashMessenger $messenger */
    protected $messenger;

    /** @var UserServiceInterface $userService */
    protected $userService;

    /**
     * IdentityMiddleware constructor.
     * @param AuthenticationService $authenticationService
     * @param AuthorizationInterface $authorization
     * @param array $authorizationConfig
     * @param RouterInterface $router
     * @param FlashMessenger $messenger
     * @param UserServiceInterface $userService
     *
     * @Inject({AuthenticationService::class, AuthorizationInterface::class,
     *      "config.authorization", RouterInterface::class, FlashMessenger::class, UserServiceInterface::class})
     */
    public function __construct(
        AuthenticationService $authenticationService,
        AuthorizationInterface $authorization,
        array $authorizationConfig,
        RouterInterface $router,
        FlashMessenger $messenger,
        UserServiceInterface $userService
    ) {
        $this->authenticationService = $authenticationService;
        $this->authorization = $authorization;
        $this->authorizationConfig = $authorizationConfig;
        $this->router = $router;
        $this->messenger = $messenger;
        $this->userService = $userService;
    }

    /**
     * @return AuthorizationInterface
     */
    public function getAuthorization(): AuthorizationInterface
    {
        return $this->authorization;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var User $user */
        $user = $this->authenticationService->getIdentity() ?? null;

        if (!is_null($user)) {
            $userRoles = $user->getRoles();
            $userRoles = array_map(function (UserRole $userRole) {
                return $userRole->getName();
            }, $userRoles);
        } else {
            // get user roles by email
            $emailRoles = [];
            $requestData = $request->getParsedBody();
            if (!empty($requestData['identity'])) {
                $emailRoles = $this->userService->getRoleNamesByEmail($requestData['identity']);
            }

            $userRoles = [UserRole::ROLE_GUEST];
            if (!empty($emailRoles)) {
                $userRoles = $emailRoles;
            }
        }

        $isGranted = false;
        foreach ($userRoles as $userRole) {
            if ($this->authorization->isGranted($userRole, $request)) {
                $isGranted = true;
                break;
            }
        }

        if (!$isGranted) {
            $this->messenger->addWarning(
                'You must sign in first in order to access the requested content',
                'user-login'
            );

            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        return $handler->handle(
            $request->withAttribute(UserInterface::class, $user)
        );
    }
}
