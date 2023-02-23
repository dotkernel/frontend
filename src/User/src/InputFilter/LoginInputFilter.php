<?php

declare(strict_types=1);

namespace Frontend\User\InputFilter;

use Laminas\Filter\StringTrim;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\NotEmpty;

/**
 * Class LoginInputFilter
 * @package Frontend\User\InputFilter
 */
final class LoginInputFilter extends InputFilter
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

        $password = new Input('password');
        $password->setRequired(true);
        $password->getFilterChain()
            ->attachByName(StringTrim::class);
        $password->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>Password</b> is required and cannot be empty',
            ], true);
        $this->add($password);

        $rememberMe = new Input('rememberMe');
        $password->setRequired(false);
        $rememberMe->getFilterChain()
            ->attachByName(StringTrim::class);
        $rememberMe->getValidatorChain()
            ->attachByName(NotEmpty::class, [], true);
        $this->add($rememberMe);
    }
}
