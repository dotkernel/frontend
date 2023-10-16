<?php

declare(strict_types=1);

namespace Frontend\User\InputFilter;

use Laminas\Filter\StringTrim;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;

/**
 * @template TFilteredValues
 * @extends InputFilter<TFilteredValues>
 */
class UserDetailInputFilter extends InputFilter
{
    public function init()
    {
        parent::init();

        $firstName = new Input('firstName');
        $firstName->setRequired(true);
        $firstName->getFilterChain()
            ->attachByName(StringTrim::class);
        $firstName->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>First Name</b> is required and cannot be empty',
            ], true)
            ->attachByName(StringLength::class, [
                'min'     => 8,
                'max'     => 150,
                'message' => '<b>First Name</b> must have between 8 and 150 characters',
            ], true);
        $this->add($firstName);

        $lastName = new Input('lastName');
        $lastName->setRequired(true);
        $lastName->getFilterChain()
            ->attachByName(StringTrim::class);
        $lastName->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>Last Name</b> is required and cannot be empty',
            ], true)
            ->attachByName(StringLength::class, [
                'min'     => 8,
                'max'     => 150,
                'message' => '<b>Last Name</b> must have between 8 and 150 characters',
            ], true);
        $this->add($lastName);
    }
}
