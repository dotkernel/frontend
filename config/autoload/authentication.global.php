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
                'messages' => [
                    'success' => 'Authenticated successfully.',
                    'failure_not_found' => 'Identity not found.',
                    'failure_invalid_credential' => 'Invalid credentials.',
                    'failure_deleted' => 'User is deleted.',
                    'failure_deactivated' => 'User is not activated.'
                ]
            ],
        ],
    ],
];
