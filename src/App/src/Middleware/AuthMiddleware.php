<?php

declare(strict_types=1);

namespace Frontend\App\Middleware;

use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Provider\GuardsProviderInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class AuthMiddleware
 * @package Frontend\App\Middleware
 */
final class AuthMiddleware implements MiddlewareInterface
{
    private readonly RouterInterface $router;
    private readonly FlashMessengerInterface $flashMessenger;
    private readonly GuardsProviderInterface $guardsProvider;
    private readonly RbacGuardOptions $rbacGuardOptions;

    /**
     * AuthMiddleware constructor.
     */
    public function __construct(
        RouterInterface $router,
        FlashMessengerInterface $flashMessenger,
        GuardsProviderInterface $guardsProvider,
        RbacGuardOptions $rbacGuardOptions
    ) {
        $this->router = $router;
        $this->flashMessenger = $flashMessenger;
        $this->guardsProvider = $guardsProvider;
        $this->rbacGuardOptions = $rbacGuardOptions;
    }

    public function process(ServerRequestInterface $serverRequest, RequestHandlerInterface $requestHandler): ResponseInterface
    {
        $guards = $this->guardsProvider->getGuards();

        //iterate over guards, which are sorted by priority
        //break on the first one that does not grant access

        $isGranted = $this->rbacGuardOptions->getProtectionPolicy() === GuardInterface::POLICY_ALLOW;

        foreach ($guards as $guard) {
            if (!$guard instanceof GuardInterface) {
                throw new RuntimeException("Guard is not an instance of " . GuardInterface::class);
            }

            //according to the policy, we whitelist or blacklist matched routes

            $r = $guard->isGranted($serverRequest);
            if ($r !== $isGranted) {
                $isGranted = $r;
                break;
            }
        }

        if (!$isGranted) {
            $this->flashMessenger->addWarning(
                'You must sign in first in order to access the requested content',
                'user-login'
            );

            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        return $requestHandler->handle($serverRequest);
    }
}
