<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\InputFilter;

use Frontend\User\InputFilter\UserDetailInputFilter;
use FrontendTest\Common\AbstractInputFilterTest;

class UserDetailInputFilterTest extends AbstractInputFilterTest
{
    private UserDetailInputFilter $inputFilter;

    public function setUp(): void
    {
        $this->inputFilter = new UserDetailInputFilter();
        $this->inputFilter->init();
    }

    public function testWillValidateFirstName(): void
    {
        $this->inputFilter->setData([]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('firstName', $messages);
        $this->assertIsArray($messages['firstName']);
        $this->assertArrayHasKey('isEmpty', $messages['firstName']);
        $this->assertSame(
            '<b>First Name</b> is required and cannot be empty',
            $messages['firstName']['isEmpty']
        );

        $this->inputFilter->setData(['firstName' => str_repeat('a', 1)]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('firstName', $messages);
        $this->assertIsArray($messages['firstName']);
        $this->assertArrayHasKey('stringLengthTooShort', $messages['firstName']);
        $this->assertSame(
            '<b>First Name</b> must have between 8 and 150 characters',
            $messages['firstName']['stringLengthTooShort']
        );

        $this->inputFilter->setData(['firstName' => str_repeat('a', 151)]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('firstName', $messages);
        $this->assertIsArray($messages['firstName']);
        $this->assertArrayHasKey('stringLengthTooLong', $messages['firstName']);
        $this->assertSame(
            '<b>First Name</b> must have between 8 and 150 characters',
            $messages['firstName']['stringLengthTooLong']
        );
    }

    public function testWillValidateLastName(): void
    {
        $this->inputFilter->setData([]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('lastName', $messages);
        $this->assertIsArray($messages['lastName']);
        $this->assertArrayHasKey('isEmpty', $messages['lastName']);
        $this->assertSame(
            '<b>Last Name</b> is required and cannot be empty',
            $messages['lastName']['isEmpty']
        );

        $this->inputFilter->setData(['lastName' => str_repeat('a', 1)]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('lastName', $messages);
        $this->assertIsArray($messages['lastName']);
        $this->assertArrayHasKey('stringLengthTooShort', $messages['lastName']);
        $this->assertSame(
            '<b>Last Name</b> must have between 8 and 150 characters',
            $messages['lastName']['stringLengthTooShort']
        );

        $this->inputFilter->setData(['lastName' => str_repeat('a', 151)]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('lastName', $messages);
        $this->assertIsArray($messages['lastName']);
        $this->assertArrayHasKey('stringLengthTooLong', $messages['lastName']);
        $this->assertSame(
            '<b>Last Name</b> must have between 8 and 150 characters',
            $messages['lastName']['stringLengthTooLong']
        );
    }

    public function testWillPassValidation(): void
    {
        $data = [
            'firstName' => 'first_name',
            'lastName' => 'last_name',
        ];

        $this->inputFilter->setData($data);

        $this->assertTrue($this->inputFilter->isValid());
        $this->assertSame($data['firstName'], $this->inputFilter->getValue('firstName'));
        $this->assertSame($data['lastName'], $this->inputFilter->getValue('lastName'));
    }
}