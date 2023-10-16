<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\InputFilter;

use Frontend\User\InputFilter\LoginInputFilter;
use FrontendTest\Common\AbstractInputFilterTest;

class LoginInputFilterTest extends AbstractInputFilterTest
{
    private LoginInputFilter $inputFilter;

    public function setUp(): void
    {
        $this->inputFilter = new LoginInputFilter();
        $this->inputFilter->init();
    }

    public function testWillValidateIdentity(): void
    {
        $this->inputFilter->setData([]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('identity', $messages);
        $this->assertIsArray($messages['identity']);
        $this->assertArrayHasKey('isEmpty', $messages['identity']);
        $this->assertSame(
            '<b>E-mail address</b> is required and cannot be empty',
            $messages['identity']['isEmpty']
        );

        $this->inputFilter->setData(['identity' => 'test']);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('identity', $messages);
        $this->assertIsArray($messages['identity']);
        $this->assertArrayHasKey('emailAddressInvalidFormat', $messages['identity']);
        $this->assertSame(
            '<b>E-mail address</b> is not valid',
            $messages['identity']['emailAddressInvalidFormat']
        );
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
    }

    public function testWillPassValidation(): void
    {
        $data = [
            'identity' => 'test@dotkernel.com',
            'password' => 'password',
            'rememberMe' => true,
        ];
        $this->inputFilter->setData($data);

        $this->assertTrue($this->inputFilter->isValid());
        $this->assertSame($data['identity'], $this->inputFilter->getValue('identity'));
        $this->assertSame($data['password'], $this->inputFilter->getValue('password'));
        $this->assertSame($data['rememberMe'], $this->inputFilter->getValue('rememberMe'));
    }
}