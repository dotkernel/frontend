<?php

declare(strict_types=1);

namespace Frontend\App;

use Fig\Http\Message\RequestMethodInterface;
use Frontend\App\Controller\LanguageController;
use Mezzio\Application;
use Psr\Container\ContainerInterface;

/**
 * Class RoutesDelegator
 * @package Frontend\App
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
            '/language/{action}',
            LanguageController::class,
            [RequestMethodInterface::METHOD_POST],
            'language'
        );

        return $app;
    }
}
