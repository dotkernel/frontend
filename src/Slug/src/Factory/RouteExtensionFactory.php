<?php

declare(strict_types=1);

namespace Frontend\Slug\Factory;

use Frontend\Slug\SlugInterface;
use Frontend\Slug\TwigExtension\RouteExtension;
use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class SlugExtensionFactory
 * @package Frontend\Slug\Factory
 */
class RouteExtensionFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return RouteExtension
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName): RouteExtension
    {
        return new $requestedName(
            $container->get(UrlHelper::class),
            $container->get(SlugInterface::class),
            $container->get(ServerUrlHelper::class)
        );
    }
}
