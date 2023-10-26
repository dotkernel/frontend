<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\InputFilter;

use Frontend\User\InputFilter\RequestResetPasswordInputFilter;
use FrontendTest\Common\AbstractInputFilterTest;

class RequestResetPasswordInputFilterTest extends AbstractInputFilterTest
{
    private RequestResetPasswordInputFilter $inputFilter;

    public function setUp(): void
    {
        $this->inputFilter = new RequestResetPasswordInputFilter();
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

        $this->inputFilter->setData(['identity' => 'invalid_email']);
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

    public function testWillPassValidation(): void
    {
        $data = ['identity' => 'test@dotkernel.com'];
        $this->inputFilter->setData($data);

        $this->assertTrue($this->inputFilter->isValid());
        $this->assertSame($data['identity'], $this->inputFilter->getValue('identity'));
    }
}
