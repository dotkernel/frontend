<?php

declare(strict_types=1);

namespace Frontend\Plugin\Factory;

use Frontend\Plugin\PluginManager;
use Frontend\Plugin\TemplatePlugin;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class PluginManagerFactory
 * @package Frontend\Plugin\Factory
 */
class PluginManagerFactory
{
    /**
     * @param ContainerInterface $container
     * @return PluginManager
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): PluginManager
    {
        $pluginManager = new PluginManager($container, $container->get('config')['dot_controller']['plugin_manager']);

        //register the built-in plugins, if the required component is present
        if ($container->has(TemplateRendererInterface::class)) {
            $pluginManager->setFactory('template', function (ContainerInterface $container) {
                return new TemplatePlugin($container->get(TemplateRendererInterface::class));
            });
        }

        return $pluginManager;
    }
}
