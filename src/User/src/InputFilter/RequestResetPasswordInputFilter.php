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
final class RequestResetPasswordInputFilter extends InputFilter
{
    public function init(): void
    {
        parent::init();

        $input = new Input('identity');
        $input->setRequired(true);
        $input->getFilterChain()
            ->attachByName(StringTrim::class);
        $input->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>E-mail address</b> is required and cannot be empty',
            ], true)
            ->attachByName(EmailAddress::class, [
                'message' => '<b>E-mail address</b> is not valid',
            ], true);
        $this->add($input);
    }
}
