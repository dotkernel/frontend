<?php

declare(strict_types=1);

namespace Frontend\Plugin;

use Frontend\Plugin\Factory\FormsPluginFactory;
use Frontend\Plugin\Factory\PluginManagerAwareInitializer;
use Frontend\Plugin\Factory\PluginManagerFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies'   => $this->getDependencies(),
            'dot_controller' => [
                'plugin_manager' => [
                    'factories' => [
                        'forms' => FormsPluginFactory::class,
                    ],
                ],
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories'    => [
                PluginManager::class => PluginManagerFactory::class,
                FormsPlugin::class   => FormsPluginFactory::class,
            ],
            'initializers' => [
                PluginManagerAwareInitializer::class,
            ],
        ];
    }
}
