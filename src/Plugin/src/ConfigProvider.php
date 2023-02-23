<?php

declare(strict_types=1);

namespace Frontend\Plugin;

use Frontend\Plugin\Factory\FormsPluginFactory;
use Frontend\Plugin\Factory\PluginManagerAwareInitializer;
use Frontend\Plugin\Factory\PluginManagerFactory;

/**
 * Class ConfigProvider
 * @package Frontend\Plugin
 */
final class ConfigProvider
{
    /**
     * @return array{dependencies: mixed[], dot_controller: array{plugin_manager: array{factories: array{forms: class-string<\Frontend\Plugin\Factory\FormsPluginFactory>}}}}
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),

            'dot_controller' => [
                'plugin_manager' => [
                    'factories' => [
                        'forms' => FormsPluginFactory::class,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array{factories: array<string, class-string<\Frontend\Plugin\Factory\PluginManagerFactory>|class-string<\Frontend\Plugin\Factory\FormsPluginFactory>>, initializers: array<class-string<\Frontend\Plugin\Factory\PluginManagerAwareInitializer>>}
     */
    public function getDependencies(): array
    {
        return [
            'factories' => [
                PluginManager::class => PluginManagerFactory::class,
                FormsPlugin::class => FormsPluginFactory::class
            ],
            'initializers' => [
                PluginManagerAwareInitializer::class,
            ]
        ];
    }
}
