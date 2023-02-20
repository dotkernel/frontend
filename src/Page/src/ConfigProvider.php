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
class ConfigProvider
{
    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
        ];
    }

    /**
     * @return array
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
     * @return array
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
