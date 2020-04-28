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
                'page.home',
                'page.about',
                'page.who-we-are',
                'user.login',
                'user.register',
                'account:activate',
                'account:unregister',
                'account:request-reset-password',
                'account:reset-password',
                'contact.get-form',
                'contact.save-form',
                'language.change',
            ],
            'user' => [
                'page.premium-content',
                'user.logout',
                'user.view',
                'profile.get-post'
            ],
        ],
    ]
];
