<?php

declare(strict_types=1);

namespace Frontend\User;

use Fig\Http\Message\RequestMethodInterface;
use Frontend\User\Controller\AccountController;
use Frontend\User\Controller\UserController;
use Mezzio\Application;
use Psr\Container\ContainerInterface;

class RoutesDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $callback): Application
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
