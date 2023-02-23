<?php

declare(strict_types=1);

namespace Frontend\Plugin\Factory;

use Frontend\Plugin\PluginManager;
use Frontend\Plugin\PluginManagerAwareInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class PluginManagerAwareInitializer
 * @package Frontend\Plugin\Factory
 */
final class PluginManagerAwareInitializer
{
    /**
     * @param $instance
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $instance): void
    {
        if ($instance instanceof PluginManagerAwareInterface) {
            $pluginManager = $container->get(PluginManager::class);
            $instance->setPluginManager($pluginManager);
        }
    }
}
