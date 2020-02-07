<?php

namespace Frontend\User;

use Fig\Http\Message\RequestMethodInterface;
use Frontend\User\Handler\LoginHandler;
use Frontend\User\Handler\LogoutHandler;
use Frontend\User\Handler\RegisterHandler;
use Mezzio\Application;
use Psr\Container\ContainerInterface;

/**
 * Class RoutesDelegator
 * @package Frontend\User
 */
class RoutesDelegator
{
    /**
     * @param ContainerInterface $container
     * @param $serviceName
     * @param callable $callback
     * @return Application
     */
    public function __invoke(ContainerInterface $container, $serviceName, callable $callback)
    {
        /** @var Application $app */
        $app = $callback();

        $app->route(
            '/login',
            LoginHandler::class,
            [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],
            'user.login'
        );

        $app->route(
            '/logout',
            LogoutHandler::class,
            [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],
            'user.logout'
        );

        $app->route(
            '/register',
            RegisterHandler::class,
            [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],
            'user.register'
        );

        return $app;
    }
}
