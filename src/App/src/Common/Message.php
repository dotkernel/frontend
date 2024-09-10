<?php

declare(strict_types=1);

namespace Frontend\App\Common;

class Message
{
    public const DUPLICATE_EMAIL               = 'An account with this email address already exists.';
    public const RESTRICTION_ROLES             = 'User accounts must have at least one role.';
    public const INVALID_ACTIVATION_CODE       = 'Invalid activation code.';
    public const AUTHENTICATED_SUCCESSFULLY    = 'Authenticated successfully.';
    public const INVALID_CREDENTIALS           = 'Invalid credentials.';
    public const MAIL_SENT_RESET_PASSWORD      = 'If the provided email identifies an account in our system, '
    . 'you will receive an email with further instructions on resetting your account\'s password.';
    public const MISSING_PARAMETER             = 'Missing parameter: \'%s\'';
    public const RESET_PASSWORD_EXPIRED        = 'Password reset request for hash: \'%s\' is invalid (expired).';
    public const RESET_PASSWORD_NOT_FOUND      = 'Could not find password reset request for hash: \'%s\'';
    public const RESET_PASSWORD_USED           = 'Password reset request for hash: \'%s\' is invalid (completed).';
    public const USER_ALREADY_ACTIVATED        = 'This account is already active.';
    public const USER_ALREADY_DEACTIVATED      = 'This account is already deactivated.';
    public const USER_ACTIVATED_SUCCESSFULLY   = 'Successfully activated.';
    public const USER_DEACTIVATED_SUCCESSFULLY = 'Successfully deactivated.';
    public const USER_UNREGISTER_STATUS        = 'Only pending accounts can be unregistered directly.';
    public const PASSWORD_RESET_SUCCESSFULLY   = 'Password Successfully reset.';
    public const USER_NOT_ACTIVATED            = 'User account must be activated first.';
    public const DELETE_ACCOUNT                = 'You must check delete option.';
    public const ACCOUNT_IS_DELETED            = 'Your account is deleted.';
    public const ACCOUNT_NOT_FOUND             = 'Account not found.';
}
