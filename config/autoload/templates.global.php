<?php

use Dot\Twig\Extension\AuthenticationExtension;
use Dot\Twig\Extension\AuthorizationExtension;
use Dot\Twig\Extension\FlashMessengerExtension;
use Dot\Twig\Extension\FormElementsExtension;
use Dot\Twig\Extension\NavigationExtension;

use Mezzio\Template\TemplateRendererInterface;
use Mezzio\Twig\TwigEnvironmentFactory;
use Mezzio\Twig\TwigRendererFactory;

return [
    'dependencies' => [
        'factories' => [
            Twig\Environment::class => TwigEnvironmentFactory::class,
            TemplateRendererInterface::class => TwigRendererFactory::class,
        ],
    ],

    'templates' => [
        'extension' => 'html.twig',
    ],

    'twig' => [
        'cache_dir' => 'data/cache/twig',
        'assets_url' => '/',
        'assets_version' => null,
        'extensions' => [
            // extension service names or instances

            // already declared in delegator
            // AuthenticationExtension::class,
            // AuthorizationExtension::class,
            // FlashMessengerExtension::class,
            // FormElementsExtension::class,
            // NavigationExtension::class,
        ],
        'runtime_loaders' => [
            // runtime loader names or instances
        ],
        'globals' => [
            // Variables to pass to all twig templates
        ],
        // 'timezone' => 'default timezone identifier; e.g. America/Chicago',
    ],
];
