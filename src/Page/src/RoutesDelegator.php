<?php

declare(strict_types=1);

namespace Frontend\Page;

use Fig\Http\Message\RequestMethodInterface;
use Frontend\Page\Controller\PageController;
use Mezzio\Application;
use Psr\Container\ContainerInterface;

/**
 * Class RoutesDelegator
 * @package Frontend\Page
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
