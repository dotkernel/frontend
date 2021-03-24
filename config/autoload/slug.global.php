<?php

return [

    /**
     * Dotkernel slug module configuration.
     *
     * How to set a slug for registered routes:
     *
     *  'slug_route' => [
     *     [
     *       'route' => 'contact',     <- route name
     *       'action' => 'form',       <- route action
     *       'alias' => '/contact',    <- route alias, this will replace /routePath/action
     *       'exchange' => [           <- if you want to exchange your route attribute specify
     *                                      the exchange configuration
     *         'hash' => [                          <- attribute name
     *              'table' => 'user',              <- table name
     *              'identifier' => 'uuid',         <- main attribute identifier ex. id, uuid
     *              'exchangeColumn' => 'identity', <- exchange value from witch the slug will be generated
     *              'slugColumn' => 'slug'          <- slug column where the slug will be stored
     *           ]
     *        ]
     *     ],
     *
     *  Use UrlHelperPlugin::class to generate url, this class can detect slug.
     */

    'slug_configuration' => [
        'detect_duplicates' => true,
        'slug_route' => [
            [
                'route' => 'contact',
                'action' => 'form',
                'alias' => '/contact',
                'exchange' => []
            ],
            [
                'route' => 'page',
                'action' => 'home',
                'alias' => '/home',
                'exchange' => []
            ],
            [
                'route' => 'account',
                'action' => 'details',
                'alias' => '/me',
                'exchange' => [
                    'hash' => [
                        'table' => 'user',
                        'identifier' => 'uuid',
                        'exchangeColumn' => 'identity',
                        'slugColumn' => 'slug'
                    ]
                ]
            ],
            [
                'route' => 'account',
                'action' => 'avatar',
                'alias' => '/avatar',
                'exchange' => []
            ],
            [
                'route' => 'account',
                'action' => 'change-password',
                'alias' => '/change-password',
                'exchange' => []
            ],
            [
                'route' => 'account',
                'action' => 'delete-account',
                'alias' => '/delete-account',
                'exchange' => []
            ]
        ]
    ]
];
