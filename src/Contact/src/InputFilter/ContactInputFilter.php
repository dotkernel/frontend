<?php

declare(strict_types=1);

namespace Frontend\Contact\InputFilter;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Session\Container;
use Laminas\Session\Validator\Csrf;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;

/**
 * @template TFilteredValues
 * @extends InputFilter<TFilteredValues>
 */
class ContactInputFilter extends InputFilter
{
    public function init(): void
    {
        parent::init();

        $email = new Input('email');
        $email->setRequired(true);
        $email->getFilterChain()
            ->attachByName(StringTrim::class)
            ->attachByName(StripTags::class);
        $email->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => 'E-mail address is required and cannot be empty',
            ], true)
            ->attachByName(EmailAddress::class, [
                'message' => 'E-mail address is invalid',
            ], true);
        $this->add($email);

        $name = new Input('name');
        $name->setRequired(true);
        $name->getFilterChain()
            ->attachByName(StringTrim::class)
            ->attachByName(StripTags::class);
        $name->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => 'Name is required and cannot be empty',
            ], true)
            ->attachByName(StringLength::class, [
                'max'     => 255,
                'message' => 'Name must not be greater than 255 characters long.',
            ], true);
        $this->add($name);

        $subject = new Input('subject');
        $subject->setRequired(false);
        $subject->getFilterChain()
            ->attachByName(StringTrim::class)
            ->attachByName(StripTags::class);
        $subject->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => 'Subject is required and cannot be empty',
            ], true)
            ->attachByName(StringLength::class, [
                'max' => 500,
            ], true);
        $this->add($subject);

        $message = new Input('message');
        $message->setRequired(true);
        $message->getFilterChain()
            ->attachByName(StringTrim::class)
            ->attachByName(StripTags::class);
        $message->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => 'Message is required and cannot be empty',
            ], true)
            ->attachByName(StringLength::class, [
                'max'     => 1000,
                'message' => 'Message must not be greater than 1000 characters long.',
            ], true);
        $this->add($message);

        $csrf = new Input('contactCsrf');
        $csrf->setRequired(true);
        $csrf->getFilterChain()
            ->attachByName(StringTrim::class)
            ->attachByName(StripTags::class);
        $csrf->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => 'CSRF is required and cannot be empty',
            ], true)
            ->attachByName(Csrf::class, [
                'name'    => 'contactCsrf',
                'message' => 'CSRF is invalid',
                'session' => new Container(),
            ], true);
        $this->add($csrf);
    }
}
