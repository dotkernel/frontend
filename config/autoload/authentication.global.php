<?php

use Frontend\User\Entity\UserEntity;
use Dot\Hydrator\ClassMethodsCamelCase;
use Dot\User\Service\PasswordCheck;

return [
    'dot_authentication' => [
        'adapter' => [
            'type' => 'CallbackCheck',
            'options' => [
                'adapter' => 'database',

                'identity_prototype' => UserEntity::class,
                'identity_hydrator' => ClassMethodsCamelCase::class,

                'table' => 'user',

                'identity_columns' => ['username', 'email'],
                'credential_column' => 'password',

                'callback_check' => PasswordCheck::class
            ]
        ],
        'storage' => [
            'type' => 'Session',
            'options' => [
                'namespace' => 'frontend_authentication',
                'member' => 'storage',
            ]
        ],

        'adapter_manager' => [
            //register custom adapters here, like you would do in a normal container
        ],

        'storage_manager' => [
            //register custom storage adapters
        ],

        'resolver_manager' => [
            //define custom http authentication resolvers here
        ]
    ]
];
