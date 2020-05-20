<?php

declare(strict_types=1);

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
                                    'route' => 'account',
                                    'actions' => [
                                        'unregister',
                                        'requestResetPassword',
                                        'resetPassword',
                                        'avatar',
                                        'details',
                                        'changePassword',
                                        'deleteAccount'
                                    ],
                                    'permissions' => ['user']
                                ],
                                [
                                    'route' => 'page',
                                    'actions' => [
                                        'premium-content'
                                    ],
                                     'permissions' => ['user']
                                ]
                            ],
                        ]
                    ]
                ]
            ]
        ],
    ]
];