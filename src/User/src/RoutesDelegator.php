<?php

declare(strict_types=1);

namespace Frontend\User;

use Fig\Http\Message\RequestMethodInterface;
use Frontend\User\Controller\AccountController;
use Frontend\User\Controller\UserController;
use Mezzio\Application;
use Psr\Container\ContainerInterface;

/**
 * Class RoutesDelegator
 * @package Frontend\User
 */
final class RoutesDelegator
{
    /**
     * @param $serviceName
     */
    public function __invoke(ContainerInterface $container, $serviceName, callable $callback): Application
    {
        /** @var Application $app */
        $app = $callback();

        $app->route(
            '/user[/{action}]',
            UserController::class,
            [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],
            'user'
        );

        $app->route(
            '/account[/{action}[/{hash}]]',
            AccountController::class,
            [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],
            'account'
        );

        return $app;
    }
}
