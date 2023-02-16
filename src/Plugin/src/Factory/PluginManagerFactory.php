<?php

declare(strict_types=1);

namespace Frontend\Plugin\Factory;

use Frontend\Plugin\PluginManager;
use Frontend\Plugin\TemplatePlugin;
use Frontend\Plugin\UrlHelperPlugin;
use Frontend\Slug\SlugInterface;
use Mezzio\Helper\UrlHelper;
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

        //register the built in plugins, if the required component is present
        if ($container->has(UrlHelper::class) && $container->has(SlugInterface::class)) {
            $pluginManager->setFactory('url', function (ContainerInterface $container) {
                return new UrlHelperPlugin(
                    $container->get(UrlHelper::class),
                    $container->get(SlugInterface::class)
                );
            });
        }

        if ($container->has(TemplateRendererInterface::class)) {
            $pluginManager->setFactory('template', function (ContainerInterface $container) {
                return new TemplatePlugin($container->get(TemplateRendererInterface::class));
            });
        }

        return $pluginManager;
    }
}
