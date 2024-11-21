<?php

declare(strict_types=1);

namespace Frontend\User\Enum;

enum UserResetPasswordStatusEnum: string
{
    case Completed = 'completed';
    case Requested = 'requested';
}
