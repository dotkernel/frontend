<?php

return [
    'dependencies' => [
        'factories' => [
            'Zend\Expressive\FinalHandler' =>
                Zend\Expressive\Container\TemplatedErrorHandlerFactory::class,

            Zend\Expressive\Template\TemplateRendererInterface::class =>
                Zend\Expressive\Twig\TwigRendererFactory::class,
        ],
    ],

    'templates' => [
        'extension' => 'html.twig',
        'paths' => [
            'app' => [__DIR__ . '/../../templates/app'],
            'layout' => [__DIR__ . '/../../templates/layout'],
            'error' => [__DIR__ . '/../../templates/error'],
            'page' => [__DIR__ . '/../../templates/page'],
            'partial' => [__DIR__ . '/../../templates/partial'],
            'user' => [__DIR__ . '/../../templates/user'],
        ],
    ],

    'twig' => [
        'cache_dir' => __DIR__ . '/../../data/cache/twig',
        'assets_url' => '/',
        'assets_version' => null,
        'extensions' => [
            // extension service names or instances
        ],
        'globals' => [

        ],
    ],

    //these are zend view helpers registered under twig
    //using the twig fallback function to request unknown twig extensions from the view helper plugin manager
    'view_helpers' => [

    ],
];
