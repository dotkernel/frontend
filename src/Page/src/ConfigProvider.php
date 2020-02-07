<?php

declare(strict_types=1);

namespace Frontend\Page;

use Dot\AnnotatedServices\Factory\AnnotatedServiceFactory;
use Frontend\Page\Handler\ContactHandler;
use Frontend\Page\Handler\HomeHandler;

/**
 * Class ConfigProvider
 * @package Frontend\Page
 */
class ConfigProvider
{
    /**
     * @return array
     */
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * @return array
     */
    public function getDependencies() : array
    {
        return [
            'factories'  => [
                HomeHandler::class => AnnotatedServiceFactory::class,
                ContactHandler::class => AnnotatedServiceFactory::class,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getTemplates() : array
    {
        return [
            'paths' => [
                'page' => [__DIR__ . '/../templates/page']
            ],
        ];
    }
}
