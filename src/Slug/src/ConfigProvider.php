<?php

declare(strict_types=1);

namespace Frontend\Slug;

use Dot\AnnotatedServices\Factory\AnnotatedServiceFactory;
use Frontend\Slug\Factory\RouteExtensionFactory;
use Frontend\Slug\Factory\SlugCollectorFactory;
use Frontend\Slug\Middleware\SlugMiddleware;
use Frontend\Slug\Service\SlugService;
use Frontend\Slug\Service\SlugServiceInterface;
use Frontend\Slug\TwigExtension\RouteExtension;

/**
 * Class ConfigProvider
 * @package Frontend\Slug
 */
final class ConfigProvider
{
    /**
     * @return array{dependencies: mixed[]}
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * @return array{factories: array<string, class-string<\Frontend\Slug\Factory\SlugCollectorFactory>|class-string<\Dot\AnnotatedServices\Factory\AnnotatedServiceFactory>|class-string<\Frontend\Slug\Factory\RouteExtensionFactory>>, aliases: array<string, class-string<\Frontend\Slug\SlugCollector>|class-string<\Frontend\Slug\Service\SlugService>>}
     */
    public function getDependencies(): array
    {
        return [
            'factories' => [
                SlugCollector::class => SlugCollectorFactory::class,
                SlugMiddleware::class => AnnotatedServiceFactory::class,
                SlugService::class => AnnotatedServiceFactory::class,
                RouteExtension::class => RouteExtensionFactory::class,
            ],
            'aliases' => [
                SlugInterface::class => SlugCollector::class,
                SlugServiceInterface::class => SlugService::class
            ],
        ];
    }
}
