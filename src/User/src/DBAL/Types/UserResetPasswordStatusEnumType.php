<?php

declare(strict_types=1);

namespace Frontend\User\DBAL\Types;

use Frontend\App\DBAL\Types\AbstractEnumType;
use Frontend\User\Enum\UserResetPasswordStatusEnum;

class UserResetPasswordStatusEnumType extends AbstractEnumType
{
    public const NAME = 'user_reset_password_status_enum';

    protected function getEnumClass(): string
    {
        return UserResetPasswordStatusEnum::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
