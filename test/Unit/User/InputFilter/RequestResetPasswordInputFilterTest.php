<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\InputFilter;

use Frontend\User\InputFilter\RequestResetPasswordInputFilter;
use FrontendTest\Common\AbstractInputFilterTest;
use Laminas\Session\Container;
use Laminas\Session\Validator\Csrf;

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
        $hash = (new Csrf(['session' => new Container()]))->getHash();

        $data = ['identity' => 'test@dotkernel.com', 'userRequestResetPasswordCsrf' => $hash];
        $this->inputFilter->setData($data);

        $this->assertTrue($this->inputFilter->isValid());
        $this->assertSame($data['identity'], $this->inputFilter->getValue('identity'));
    }
}
