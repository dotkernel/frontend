<?php

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
                    'subscriber' => [
                        'permissions' => [
                            'premium-content',
                        ]
                    ]
                ]
            ]
        ],

        'assertion_manager' => [],
        'assertions' => []
    ]
];
