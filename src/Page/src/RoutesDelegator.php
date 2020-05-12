<?php

namespace Frontend\Page;

use Fig\Http\Message\RequestMethodInterface;
use Frontend\Page\Controller\PageController;
use Mezzio\Application;
use Psr\Container\ContainerInterface;

/**
 * Class RoutesDelegator
 * @package Frontend\Page
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

        $app->get('/', [PageController::class], 'home');

        $app->route(
            '/page[/{action}]',
            [PageController::class],
            [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],
            'page'
        );

        return $app;
    }
}
