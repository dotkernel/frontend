<?php

declare(strict_types=1);

namespace Frontend\App\Common;

use Frontend\User\Entity\User;

/**
 * Class UserAuthentication
 * @package Frontend\App\Middleware
 */
class UserAuthentication
{
    public static function verifyCredential(User $user, $inputPassword)
    {
        return password_verify($inputPassword, $user->getPassword());
    }
}
