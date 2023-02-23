<?php

declare(strict_types=1);

namespace Frontend\Page;

use Dot\AnnotatedServices\Factory\AnnotatedServiceFactory;
use Frontend\Page\Controller\PageController;
use Frontend\Page\Service\PageService;
use Frontend\Page\Service\PageServiceInterface;

/**
 * Class ConfigProvider
 * @package Frontend\Page
 */
final class ConfigProvider
{
    /**
     * @return array{dependencies: mixed[], templates: mixed[]}
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
        ];
    }

    /**
     * @return array{factories: array<string, class-string<\Dot\AnnotatedServices\Factory\AnnotatedServiceFactory>>, aliases: array<string, class-string<\Frontend\Page\Service\PageService>>}
     */
    public function getDependencies(): array
    {
        return [
            'factories' => [
                PageController::class => AnnotatedServiceFactory::class,
                PageService::class => AnnotatedServiceFactory::class,
            ],
            'aliases' => [
                PageServiceInterface::class => PageService::class,
            ],
        ];
    }

    /**
     * @return array{paths: array{page: string[]}}
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'page' => [__DIR__ . '/../templates/page']
            ],
        ];
    }
}
