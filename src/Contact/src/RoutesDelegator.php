<?php

namespace Frontend\Contact;

use Fig\Http\Message\RequestMethodInterface;
use Frontend\Contact\Handler\ContactHandler;
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

        $app->get(
            '/contact/[{action}]',
            ContactHandler::class,
            'contact.get-form'
        );

        $app->post(
            '/contact/[{action}]',
            ContactHandler::class,
            'contact.save-form'
        );

        return $app;
    }
}
