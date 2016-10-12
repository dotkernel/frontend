<?php

namespace Dot\Frontend\Action;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class HomePageFactory
 * @package Dot\Frontend\Action
 */
class HomePageFactory
{
    /**
     * @param ContainerInterface $container
     * @return HomePageAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $router   = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;

        return new HomePageAction($router, $template);
    }
}
