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
            'middleware' => \Dot\Frontend\Action\HomePageAction::class,
            'allowed_methods' => ['GET'],
        ],

        'user_route' => [
            'middleware' => [
                //add our user controller for additional actions
                \Dot\Frontend\User\Controller\UserController::class,
            ]
        ],

        [
            'name' => 'page',
            'path' => '/page[/{action}]',
            'middleware' => \Dot\Frontend\Controller\PageController::class,
        ],
    ],
];
