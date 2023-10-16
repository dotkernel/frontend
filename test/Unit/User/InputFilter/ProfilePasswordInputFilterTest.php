<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\InputFilter;

use Frontend\User\InputFilter\ProfilePasswordInputFilter;
use FrontendTest\Common\AbstractInputFilterTest;
use PHPUnit\Framework\TestCase;
class ProfilePasswordInputFilterTest extends AbstractInputFilterTest
{
    private ProfilePasswordInputFilter $inputFilter;

    public function setUp(): void
    {
        $this->inputFilter = new ProfilePasswordInputFilter();
        $this->inputFilter->init();
    }

    public function testWillValidatePassword(): void
    {
        $this->inputFilter->setData([]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('password', $messages);
        $this->assertIsArray($messages['password']);
        $this->assertArrayHasKey('isEmpty', $messages['password']);
        $this->assertSame(
            '<b>Password</b> is required and cannot be empty',
            $messages['password']['isEmpty']
        );

        $this->inputFilter->setData(['password' => str_repeat('a', 7)]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('password', $messages);
        $this->assertIsArray($messages['password']);
        $this->assertArrayHasKey('stringLengthTooShort', $messages['password']);
        $this->assertSame(
            '<b>Password</b> must have between 8 and 150 characters',
            $messages['password']['stringLengthTooShort']
        );

        $this->inputFilter->setData(['password' => str_repeat('a', 151)]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('password', $messages);
        $this->assertIsArray($messages['password']);
        $this->assertArrayHasKey('stringLengthTooLong', $messages['password']);
        $this->assertSame(
            '<b>Password</b> must have between 8 and 150 characters',
            $messages['password']['stringLengthTooLong']
        );
    }

    public function testWillValidateConfirmPassword(): void
    {
        $this->inputFilter->setData([]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('passwordConfirm', $messages);
        $this->assertIsArray($messages['passwordConfirm']);
        $this->assertArrayHasKey('isEmpty', $messages['passwordConfirm']);
        $this->assertSame(
            '<b>Confirm Password</b> is required and cannot be empty',
            $messages['passwordConfirm']['isEmpty']
        );

        $this->inputFilter->setData(['passwordConfirm' => str_repeat('a', 7)]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('passwordConfirm', $messages);
        $this->assertIsArray($messages['passwordConfirm']);
        $this->assertArrayHasKey('stringLengthTooShort', $messages['passwordConfirm']);
        $this->assertSame(
            '<b>Confirm Password</b> must have between 8 and 150 characters',
            $messages['passwordConfirm']['stringLengthTooShort']
        );

        $this->inputFilter->setData(['passwordConfirm' => str_repeat('a', 151)]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('passwordConfirm', $messages);
        $this->assertIsArray($messages['passwordConfirm']);
        $this->assertArrayHasKey('stringLengthTooLong', $messages['passwordConfirm']);
        $this->assertSame(
            '<b>Confirm Password</b> must have between 8 and 150 characters',
            $messages['passwordConfirm']['stringLengthTooLong']
        );


        $this->inputFilter->setData([
            'password' => 'password',
            'passwordConfirm' => 'invalid_password'
        ]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('passwordConfirm', $messages);
        $this->assertIsArray($messages['passwordConfirm']);
        $this->assertArrayHasKey('notSame', $messages['passwordConfirm']);
        $this->assertSame(
            '<b>Password confirm</b> does not match',
            $messages['passwordConfirm']['notSame']
        );
    }

    public function testWillPassValidation(): void
    {
        $data = [
            'password' => 'password',
            'passwordConfirm' => 'password'
        ];

        $this->inputFilter->setData($data);
        $this->assertTrue($this->inputFilter->isValid());
        $this->assertSame($data['password'], $this->inputFilter->getValue('password'));
        $this->assertSame($data['passwordConfirm'], $this->inputFilter->getValue('passwordConfirm'));
    }
}