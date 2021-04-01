<?php

declare(strict_types=1);

namespace Frontend\Slug\Factory;

use Frontend\Slug\Service\SlugServiceInterface;
use Mezzio\Helper\UrlHelper;
use Mezzio\Router\RouterInterface;
use Psr\Container\ContainerInterface;
use Frontend\Slug\SlugCollector;

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
     */
    public function __invoke(ContainerInterface $container, $requestedName): SlugCollector
    {
        $config             = $container->get('config')['slug_configuration'] ?? [];
        $router             = $container->get(RouterInterface::class);
        $url                = $container->get(UrlHelper::class);
        $slugService        = $container->get(SlugServiceInterface::class);

        $detectDuplicates   = true;
        if (isset($config['detect_duplicates'])) {
            $detectDuplicates   = $config['detect_duplicates'];
        }

        return new $requestedName(
            $router,
            $url,
            $slugService,
            $config,
            $detectDuplicates
        );
    }
}
