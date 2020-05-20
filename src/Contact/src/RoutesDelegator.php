<?php

namespace Frontend\Contact;

use Fig\Http\Message\RequestMethodInterface;
use Frontend\Contact\Controller\ContactController;
use Mezzio\Application;
use Psr\Container\ContainerInterface;

/**
 * Class RoutesDelegator
 * @package Frontend\Contact
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
            '/contact/[{action}]',
            ContactController::class,
            [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],
            'contact'
        );

        return $app;
    }
}
