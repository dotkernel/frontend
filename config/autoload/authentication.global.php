<?php

use Frontend\User\Entity\User;
use Frontend\App\Common\Message;

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
                        'message' => Message::USER_NOT_ACTIVATED
                    ],
                    'isDeleted' => [
                        'value' => false,
                        'message' => Message::IS_DELETED
                    ]
                ],
            ],
        ],
    ],
];
