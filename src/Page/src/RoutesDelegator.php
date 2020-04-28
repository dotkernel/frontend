<?php

namespace Frontend\Page;

use Fig\Http\Message\RequestMethodInterface;
use Frontend\Page\Handler\AboutHandler;
use Frontend\Page\Handler\HomeHandler;
use Frontend\Page\Handler\PremiumContentHandler;
use Frontend\Page\Handler\WhoWeAreHandler;
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

        $app->get('/', HomeHandler::class, 'page.home');

        $app->route(
            '/about',
            [AboutHandler::class],
            [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],
            'page.about'
        );

        $app->route(
            '/who-we-are',
            [WhoWeAreHandler::class],
            [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],
            'page.who-we-are'
        );

        $app->route(
            '/premium-content',
            [PremiumContentHandler::class],
            [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],
            'page.premium-content'
        );

        return $app;
    }
}
