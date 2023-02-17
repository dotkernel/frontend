<?php

declare(strict_types=1);

namespace Frontend\Slug\Factory;

use Frontend\Slug\Service\SlugServiceInterface;
use Mezzio\Helper\UrlHelper;
use Mezzio\Router\RouterInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Frontend\Slug\SlugCollector;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class SlugCollectorFactory
 * @package Frontend\Slug\Factory
 */
class SlugCollectorFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return SlugCollector
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName): SlugCollector
    {
        $config = $container->get('config')['slug_configuration'] ?? [];

        return new $requestedName(
            $container->get(RouterInterface::class),
            $container->get(UrlHelper::class),
            $container->get(SlugServiceInterface::class),
            $config,
            $config['detect_duplicates'] ?? true
        );
    }
}
