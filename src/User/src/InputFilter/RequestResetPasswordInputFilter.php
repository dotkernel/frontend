<?php

declare(strict_types=1);

namespace Frontend\User\InputFilter;

use Laminas\Filter\StringTrim;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\NotEmpty;

/**
 * Class RequestResetPasswordInputFilter
 * @package Frontend\User\InputFilter
 */
class RequestResetPasswordInputFilter extends InputFilter
{
    public function init()
    {
        parent::init();

        $identity = new Input('identity');
        $identity->setRequired(true);
        $identity->getFilterChain()
            ->attachByName(StringTrim::class);
        $identity->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>E-mail address</b> is required and cannot be empty',
            ], true)
            ->attachByName(EmailAddress::class, [
                'message' => '<b>E-mail address</b> is not valid',
            ], true);
        $this->add($identity);
    }
}
