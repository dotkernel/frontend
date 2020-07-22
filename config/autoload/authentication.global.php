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
                    'not_found' => 'Identity not found.',
                    'invalid_credential' => 'Invalid credentials.',
                 ],
                'options' => [
                    'status' => [
                        'value' => User::STATUS_ACTIVE,
                        'message' => 'User not activated.'
                    ],
                    'isDeleted' => [
                        'value' => false,
                        'message' => 'User is deleted.'
                    ]
                ],
            ],
        ],
    ],
];
