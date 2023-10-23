<?php

declare(strict_types=1);

namespace Frontend\Page;

use Dot\AnnotatedServices\Factory\AnnotatedServiceFactory;
use Frontend\Page\Controller\PageController;
use Frontend\Page\Service\PageService;
use Frontend\Page\Service\PageServiceInterface;
use Mezzio\Application;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class => [
                    RoutesDelegator::class,
                ],
            ],
            'factories'  => [
                PageController::class => AnnotatedServiceFactory::class,
                PageService::class    => AnnotatedServiceFactory::class,
            ],
            'aliases'    => [
                PageServiceInterface::class => PageService::class,
            ],
        ];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [
                'page' => [__DIR__ . '/../templates/page'],
            ],
        ];
    }
}
