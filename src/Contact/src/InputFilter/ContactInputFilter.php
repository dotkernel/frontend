<?php

declare(strict_types=1);

namespace Frontend\Contact\InputFilter;

use Laminas\Filter\StringTrim;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;

/**
 * @template TFilteredValues
 * @extends InputFilter<TFilteredValues>
 */
class ContactInputFilter extends InputFilter
{
    /**
     * @return void
     */
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

        $name = new Input('name');
        $name->setRequired(true);
        $name->getFilterChain()
            ->attachByName(StringTrim::class);
        $name->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>Name</b> is required and cannot be empty',
            ], true)
            ->attachByName(StringLength::class, [
                'max' => 255,
            ], true);
        $this->add($name);

        $subject = new Input('subject');
        $subject->setRequired(false);
        $subject->getFilterChain()
            ->attachByName(StringTrim::class);
        $subject->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>Subject</b> is required and cannot be empty',
            ], true)
            ->attachByName(StringLength::class, [
                'max' => 500,
            ], true);
        $this->add($subject);

        $message = new Input('message');
        $message->setRequired(true);
        $message->getFilterChain()
            ->attachByName(StringTrim::class);
        $message->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>Message</b> is required and cannot be empty',
            ], true)
            ->attachByName(StringLength::class, [
                'max' => 1000,
            ], true);
        $this->add($message);
    }
}
