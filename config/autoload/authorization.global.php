<?php

declare(strict_types=1);

return [
    'authorization' => [],
    'mezzio-authorization-rbac' => [
        'roles' => [
            'admin' => [],
            'user' => [],
            'guest'  => ['user'],
        ],
        'permissions' => [
            'guest' => [
                'page',
                'user',
                'language',
                'contact'
            ],
            'user' => [
                'account'
            ],
        ],
    ]
];
