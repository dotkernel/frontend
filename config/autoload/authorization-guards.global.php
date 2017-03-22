<?php

use Dot\Rbac\Guard\Guard\GuardInterface;

return [
    'dot_authorization' => [
        'protection_policy' => GuardInterface::POLICY_ALLOW,

        'event_listeners' => [],

        'guards_provider_manager' => [],
        'guard_manager' => [],

        'guards_provider' => [
            'type' => 'ArrayGuards',
            'options' => [
                'guards' => [
                    [
                        'type' => 'ControllerPermission',
                        'options' => [
                            'rules' => [
                                [
                                    'route' => 'user',
                                    'actions' => [
                                        'register',
                                        'reset-password',
                                        'forgot-password',
                                        'confirm-account',
                                        'opt-out',
                                        'pending-activation',
                                        'resend-activation'
                                    ],
                                    'permissions' => ['*']
                                ],
                                [
                                    'route' => 'user',
                                    'actions' => [],
                                    'permissions' => ['authenticated']
                                ],
                                [
                                    'route' => 'page',
                                    'actions' => ['premium-content'],
                                    'permissions' => ['premium-content']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
    ]
];
