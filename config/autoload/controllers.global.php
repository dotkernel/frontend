<?php

return [

    'dependencies' => [
        'factories' => [
            \Dot\Frontend\User\Controller\UserController::class =>
                \Dot\Frontend\User\Factory\UserControllerFactory::class,

            \Dot\Frontend\Controller\PageController::class =>
                \Dot\Frontend\Factory\Controller\PageControllerFactory::class,
        ]
    ],

    'dot_controller' => [

        'plugin_manager' => []
    ],
];
