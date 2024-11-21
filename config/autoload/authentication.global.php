<?php

declare(strict_types=1);

use Frontend\App\Common\Message;
use Frontend\User\Entity\User;
use Frontend\User\Enum\UserStatusEnum;

return [
    'doctrine' => [
        'authentication' => [
            'orm_default' => [
                'object_manager'      => 'doctrine.entity_manager.orm_default',
                'identity_class'      => User::class,
                'identity_property'   => 'identity',
                'credential_property' => 'password',
                'messages'            => [
                    'success'            => Message::AUTHENTICATED_SUCCESSFULLY,
                    'not_found'          => Message::ACCOUNT_NOT_FOUND,
                    'invalid_credential' => Message::INVALID_CREDENTIALS,
                ],
                'options'             => [
                    'status'    => [
                        'value'   => UserStatusEnum::Active,
                        'message' => Message::USER_NOT_ACTIVATED,
                    ],
                    'isDeleted' => [
                        'value'   => false,
                        'message' => Message::ACCOUNT_NOT_FOUND,
                    ],
                ],
            ],
        ],
    ],
];
