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
use Laminas\Validator\Identical;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;

/**
 * @template TFilteredValues
 * @extends InputFilter<TFilteredValues>
 */
class RegisterInputFilter extends InputFilter
{
    public function init(): void
    {
        parent::init();

        $email = new Input('email');
        $email->setRequired(true);
        $email->getFilterChain()
            ->attachByName(StringTrim::class);
        $email->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>E-mail address</b> is required and cannot be empty',
            ], true)
            ->attachByName(EmailAddress::class, [
                'message' => '<b>E-mail address</b> is invalid',
            ], true);
        $this->add($email);

        $password = new Input('password');
        $password->setRequired(true);
        $password->getFilterChain()
            ->attachByName(StringTrim::class);
        $password->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>Password</b> is required and cannot be empty',
            ], true)
            ->attachByName(StringLength::class, [
                'min'     => 8,
                'max'     => 150,
                'message' => '<b>Password</b> must have between 8 and 150 characters',
            ], true);
        $this->add($password);

        $passwordConfirm = new Input('passwordConfirm');
        $passwordConfirm->setRequired(true);
        $passwordConfirm->getFilterChain()
            ->attachByName(StringTrim::class);
        $passwordConfirm->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>Confirm Password</b> is required and cannot be empty',
            ], true)
            ->attachByName(StringLength::class, [
                'min'     => 8,
                'max'     => 150,
                'message' => '<b>Confirm Password</b> must have between 8 and 150 characters',
            ])
            ->attachByName(Identical::class, [
                'token'   => 'password',
                'message' => '<b>Confirm Password</b> does not match',
            ]);
        $this->add($passwordConfirm);

        $csrf = new Input('userRegisterCsrf');
        $csrf->setRequired(true);
        $csrf->getFilterChain()
            ->attachByName(StringTrim::class)
            ->attachByName(StripTags::class);
        $csrf->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>CSRF</b> is required and cannot be empty',
            ], true)
            ->attachByName(Csrf::class, [
                'name'    => 'userRegisterCsrf',
                'message' => '<b>CSRF</b> is invalid',
                'session' => new Container(),
            ], true);
        $this->add($csrf);
    }
}
