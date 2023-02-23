<?php

declare(strict_types=1);

namespace Frontend\Contact;

use Fig\Http\Message\RequestMethodInterface;
use Frontend\Contact\Controller\ContactController;
use Mezzio\Application;
use Psr\Container\ContainerInterface;

/**
 * Class RoutesDelegator
 * @package Frontend\Contact
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

        $app->route(
            '/contact/[{action}]',
            ContactController::class,
            [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],
            'contact'
        );

        return $app;
    }
}
