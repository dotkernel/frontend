<?php

declare(strict_types=1);

namespace Frontend\User\Enum;

enum UserStatusEnum: string
{
    case Active  = 'active';
    case Pending = 'pending';
}
