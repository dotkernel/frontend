<?php

declare(strict_types=1);

namespace Frontend\Page;

use Fig\Http\Message\RequestMethodInterface;
use Frontend\Page\Controller\PageController;
use Frontend\Page\Handler\GetPageViewHandler;
use Mezzio\Application;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class RoutesDelegator
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $serviceName, callable $callback): Application
    {
        $app = $callback();
        assert($app instanceof Application);

        $routes = $container->get('config')['routes'] ?? [];
        foreach ($routes as $prefix => $moduleRoutes) {
            foreach ($moduleRoutes as $routeUri => $templateName) {
                $app->get(
                    sprintf('/%s/%s', $prefix, $routeUri),
                    [GetPageViewHandler::class],
                    sprintf('%s::%s', $prefix, $templateName)
                );
            }
        }

        return $app;
    }
}
