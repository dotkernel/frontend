<?php

use Frontend\User\Entity\User;

return [
    'doctrine' => [
        'authentication' => [
            'orm_default' => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'identity_class' => User::class,
                'identity_property' => 'identity',
                'credential_property' => 'password',
                'credential_callable' => 'Frontend\App\Common\UserAuthentication::verifyCredential',
            ],
        ],
    ],
];
