<?php

declare(strict_types=1);

namespace Frontend\Plugin\Factory;

use Frontend\Plugin\PluginManager;
use Frontend\Plugin\PluginManagerAwareInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class PluginManagerAwareInitializer
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, ?object $instance): void
    {
        if ($instance instanceof PluginManagerAwareInterface) {
            $instance->setPluginManager(
                $container->get(PluginManager::class)
            );
        }
    }
}
