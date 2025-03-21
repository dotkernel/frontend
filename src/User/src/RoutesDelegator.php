<?php

declare(strict_types=1);

namespace Frontend\User;

use Fig\Http\Message\RequestMethodInterface;
use Frontend\User\Controller\AccountController;
use Frontend\User\Controller\UserController;
use Frontend\User\Handler\Account\GetActivateAccountHandler;
use Frontend\User\Handler\Account\GetUnregisterAccountHandler;
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

        $app->get('/account/activate/{hash}', GetActivateAccountHandler::class, 'account::activate');
        $app->get('/account/unregister/{hash}', GetUnregisterAccountHandler::class, 'account::unregister');

        $app->route(
            '/account[/{action}[/{hash}]]',
            AccountController::class,
            [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],
            'account'
        );

        return $app;
    }
}
