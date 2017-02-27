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
            'middleware' => \App\Frontend\Action\HomePageAction::class,
            'allowed_methods' => ['GET'],
        ],
        [
            'name' => 'page',
            'path' => '/page[/{action}]',
            'middleware' => \App\Frontend\Controller\PageController::class,
        ],
        [
            'name' => 'contact',
            'path' => '/contact[/[{action}]]',
            'middleware' => \App\Frontend\Controller\ContactController::class,
        ],
    ],
];
