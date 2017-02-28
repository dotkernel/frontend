<?php

return [
    'dependencies' => [
        'invokables' => [
            Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\FastRouteRouter::class,
        ],
    ],

    'routes' => [
        [
            'name' => 'home',
            'path' => '/',
            'middleware' => \Frontend\App\Action\HomePageAction::class,
            'allowed_methods' => ['GET'],
        ],
        [
            'name' => 'page',
            'path' => '/page[/{action}]',
            'middleware' => \Frontend\App\Controller\PageController::class,
        ],
        [
            'name' => 'contact',
            'path' => '/contact[/[{action}]]',
            'middleware' => \Frontend\App\Controller\ContactController::class,
        ],
    ],
];
