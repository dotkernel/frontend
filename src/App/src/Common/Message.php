<?php

declare(strict_types=1);

namespace Frontend\App\Common;

/**
 * Class Message
 * @package Frontend\App\Common
 */
final class Message
{
    /**
     * @var string
     */
    public const DUPLICATE_EMAIL = 'An account with this email address already exists.';

    /**
     * @var string
     */
    public const RESTRICTION_ROLES = 'User accounts must have at least one role.';

    /**
     * @var string
     */
    public const INVALID_ACTIVATION_CODE = 'Invalid activation code.';

    /**
     * @var string
     */
    public const INVALID_VALUE = "The value specified for '%s' is invalid.";

    /**
     * @var string
     */
    public const MAIL_SENT_RESET_PASSWORD = 'If the provided email identifies an account in our system, ' .
    "you will receive an email with further instructions on resetting your account's password.";

    /**
     * @var string
     */
    public const MAIL_SENT_USER_ACTIVATION = "User activation mail has been successfully sent to '%s'";

    /**
     * @var string
     */
    public const MISSING_PARAMETER = "Missing parameter: '%s'";

    /**
     * @var string
     */
    public const NOT_FOUND_BY_UUID = 'Unable to find %s identified by uuid: %s';

    /**
     * @var string
     */
    public const RESET_PASSWORD_EXPIRED = "Password reset request for hash: '%s' is invalid (expired).";

    /**
     * @var string
     */
    public const RESET_PASSWORD_NOT_FOUND = "Could not find password reset request for hash: '%s'";

    /**
     * @var string
     */
    public const RESET_PASSWORD_USED = "Password reset request for hash: '%s' is invalid (completed).";

    /**
     * @var string
     */
    public const RESET_PASSWORD_VALID = "Password reset request for hash: '%s' exists and is valid.";

    /**
     * @var string
     */
    public const RESOURCE_NOT_ALLOWED = 'You are not allowed to access this resource.';

    /**
     * @var string
     */
    public const RESTRICTION_IMAGE = 'File must be an image (jpg, png).';

    /**
     * @var string
     */
    public const USER_ALREADY_ACTIVATED = 'This account is already active.';

    /**
     * @var string
     */
    public const USER_ALREADY_DEACTIVATED = 'This account is already deactivated.';

    /**
     * @var string
     */
    public const USER_ACTIVATED_SUCCESSFULLY = 'Successfully activated.';

    /**
     * @var string
     */
    public const USER_DEACTIVATED_SUCCESSFULLY = 'Successfully deactivated.';

    /**
     * @var string
     */
    public const USER_UNREGISTER_STATUS = 'Only pending accounts can be unregistered directly.';

    /**
     * @var string
     */
    public const PASSWORD_RESET_SUCCESSFULLY = 'Password Successfully reset.';

    /**
     * @var string
     */
    public const USER_NOT_ACTIVATED = 'User account must be activated first.';

    /**
     * @var string
     */
    public const USER_NOT_FOUND_BY_EMAIL = "Could not find account identified by email '%s'";

    /**
     * @var string
     */
    public const VALIDATOR_REQUIRED_FIELD = 'This field is required and cannot be empty.';

    /**
     * @var string
     */
    public const VALIDATOR_REQUIRED_UPLOAD = 'A file must be uploaded first.';

    /**
     * @var string
     */
    public const DELETE_ACCOUNT = 'You must check delete option.';

    /**
     * @var string
     */
    public const IS_DELETED = 'User is deleted.';
}
