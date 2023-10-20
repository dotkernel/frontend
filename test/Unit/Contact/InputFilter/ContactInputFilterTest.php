<?php

declare(strict_types=1);

namespace FrontendTest\Unit\Contact\InputFilter;

use Frontend\Contact\InputFilter\ContactInputFilter;
use PHPUnit\Framework\TestCase;

use function str_repeat;

class ContactInputFilterTest extends TestCase
{
    private ContactInputFilter $inputFilter;

    public function setUp(): void
    {
        $this->inputFilter = new ContactInputFilter();
        $this->inputFilter->init();
    }

    public function testWillValidateEmail(): void
    {
        $this->inputFilter->setData([]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('email', $messages);
        $this->assertIsArray($messages['email']);
        $this->assertArrayHasKey('isEmpty', $messages['email']);
        $this->assertSame(
            '<b>E-mail address</b> is required and cannot be empty',
            $messages['email']['isEmpty']
        );

        $this->inputFilter->setData(['email' => 'test']);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('email', $messages);
        $this->assertIsArray($messages['email']);
        $this->assertArrayHasKey('emailAddressInvalidFormat', $messages['email']);
        $this->assertSame(
            '<b>E-mail address</b> is invalid',
            $messages['email']['emailAddressInvalidFormat']
        );
    }

    public function testWillValidateName(): void
    {
        $this->inputFilter->setData(['name' => '']);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('name', $messages);
        $this->assertIsArray($messages['name']);
        $this->assertArrayHasKey('isEmpty', $messages['name']);
        $this->assertSame(
            '<b>Name</b> is required and cannot be empty',
            $messages['name']['isEmpty']
        );

        $this->inputFilter->setData(['name' => str_repeat('a', 256)]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('name', $messages);
        $this->assertIsArray($messages['name']);
        $this->assertArrayHasKey('stringLengthTooLong', $messages['name']);
        $this->assertSame(
            '<b>Name</b> must not be greater than 255 characters long.',
            $messages['name']['stringLengthTooLong']
        );
    }

    public function testWillValidateMessage(): void
    {
        $this->inputFilter->setData(['message' => '']);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('message', $messages);
        $this->assertIsArray($messages['message']);
        $this->assertArrayHasKey('isEmpty', $messages['message']);
        $this->assertSame(
            '<b>Message</b> is required and cannot be empty',
            $messages['message']['isEmpty']
        );

        $this->inputFilter->setData(['message' => str_repeat('a', 1001)]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('message', $messages);
        $this->assertIsArray($messages['message']);
        $this->assertArrayHasKey('stringLengthTooLong', $messages['message']);
        $this->assertSame(
            '<b>Message</b> must not be greater than 1000 characters long.',
            $messages['message']['stringLengthTooLong']
        );
    }
}
