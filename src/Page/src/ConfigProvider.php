<?php

declare(strict_types=1);

namespace Frontend\Page;

use Dot\AnnotatedServices\Factory\AnnotatedServiceFactory;
use Frontend\Page\Controller\AboutHandler;
use Frontend\Page\Controller\HomeHandler;
use Frontend\Page\Controller\PageController;
use Frontend\Page\Controller\PremiumContentHandler;
use Frontend\Page\Controller\TestController;
use Frontend\Page\Controller\WhoWeAreHandler;

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
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            'factories'  => [
                PageController::class => AnnotatedServiceFactory::class,
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
