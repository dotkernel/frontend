<?php

namespace Frontend\App;

use Fig\Http\Message\RequestMethodInterface;
use Frontend\App\Handler\LanguageHandler;
use Mezzio\Application;
use Psr\Container\ContainerInterface;

/**
 * Class RoutesDelegator
 * @package Frontend\App
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
            '/language/{action}',
            LanguageHandler::class,
            [RequestMethodInterface::METHOD_POST],
            'language.change'
        );

        return $app;
    }
}
