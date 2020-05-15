<?php
//
//declare(strict_types=1);
//
//return [
//    'dependencies' => [],
//    'authorization' => [],
//    'mezzio-authorization-rbac' => [
//        'roles' => [
//            'admin' => [],
//            'user' => [],
//            'guest'  => ['user'],
//        ],
//        'permissions' => [
//            'guest' => [
//                'page',
//                'user',
//                'language',
//                'contact',
//                'test'
//            ],
//            'user' => [
//                'account'
//            ],
//        ],
//    ]
//];


return [
    'dependencies' => [],
    'dot_authorization' => [
        'guest_role' => 'guest',

        'role_provider_manager' => [],

        'role_provider' => [
            'type' => 'InMemory',
            'options' => [
                'roles' => [
                    'user' => [
                        'permissions' => [
                            'authenticated',
                        ]
                    ],
                    'guest' => [
                        'permissions' => [
                            'unauthenticated',
                        ]
                    ],
                    'admin' => [
                        'authenticated',
                    ]
                ]
            ]
        ],

        'assertion_manager' => [],
        'assertions' => []
    ]
];
