<?php

declare(strict_types=1);

namespace Frontend\Slug\Factory;

use Frontend\Slug\SlugInterface;
use Frontend\Slug\TwigExtension\RouteExtension;
use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

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
     */
    public function __invoke(ContainerInterface $container, $requestedName): RouteExtension
    {
        $url            = $container->get(UrlHelper::class);
        $slugAdapter    = $container->get(SlugInterface::class);
        $serverUrl      = $container->get(ServerUrlHelper::class);

        return new $requestedName(
            $url,
            $slugAdapter,
            $serverUrl
        );
    }
}
