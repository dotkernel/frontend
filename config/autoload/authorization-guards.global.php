<?php

return [

    'dot_authorization' => [
        'protection_policy' => \Dot\Rbac\Guard\Guard\GuardInterface::POLICY_ALLOW,

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
                                    'actions' => ['change-password', 'account'],
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
