<?php

declare(strict_types=1);

namespace Frontend\User\InputFilter;

use Laminas\Filter\StringTrim;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\Identical;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;

/**
 * Class ResetPasswordInputFilter
 * @package Frontend\User\InputFilter
 */
final class ResetPasswordInputFilter extends InputFilter
{
    public function init(): void
    {
        parent::init();

        $password = new Input('password');
        $password->setRequired(true);
        $password->getFilterChain()
            ->attachByName(StringTrim::class);
        $password->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>Password</b> is required and cannot be empty',
            ], true)
            ->attachByName(StringLength::class, [
                'min' => 8,
                'max' => 150,
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
                'min' => 8,
                'max' => 150,
                'message' => '<b>Confirm Password</b> must have between 8 and 150 characters',
            ])
            ->attachByName(Identical::class, [
                'token' => 'password',
                'message' => '<b>Password confirm</b> does not match',
            ]);
        $this->add($passwordConfirm);
    }
}
