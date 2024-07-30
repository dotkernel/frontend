<?php

declare(strict_types=1);

namespace Frontend\User\InputFilter;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Session\Container;
use Laminas\Session\Validator\Csrf;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\NotEmpty;

/**
 * @template TFilteredValues
 * @extends InputFilter<TFilteredValues>
 */
class RequestResetPasswordInputFilter extends InputFilter
{
    public function init(): void
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

        $csrf = new Input('userRequestResetPasswordCsrf');
        $csrf->setRequired(true);
        $csrf->getFilterChain()
            ->attachByName(StringTrim::class)
            ->attachByName(StripTags::class);
        $csrf->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>CSRF</b> is required and cannot be empty',
            ], true)
            ->attachByName(Csrf::class, [
                'name'    => 'userRequestResetPasswordCsrf',
                'message' => '<b>CSRF</b> is invalid',
                'session' => new Container(),
            ], true);
        $this->add($csrf);
    }
}
