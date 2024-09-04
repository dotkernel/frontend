<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\InputFilter;

use Frontend\App\Common\Message;
use Frontend\User\Entity\User;
use Frontend\User\InputFilter\ProfileDeleteInputFilter;
use FrontendTest\Common\AbstractInputFilterTest;
use Laminas\Session\Container;
use Laminas\Session\Validator\Csrf;

class ProfileDeleteInputFilterTest extends AbstractInputFilterTest
{
    private ProfileDeleteInputFilter $inputFilter;

    public function setUp(): void
    {
        $this->inputFilter = new ProfileDeleteInputFilter();
        $this->inputFilter->init();
    }

    public function testWillValidateIsDeleted(): void
    {
        $this->inputFilter->setData([]);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('isDeleted', $messages);
        $this->assertIsArray($messages['isDeleted']);
        $this->assertArrayHasKey('isEmpty', $messages['isDeleted']);
        $this->assertSame(
            Message::DELETE_ACCOUNT,
            $messages['isDeleted']['isEmpty']
        );

        $this->inputFilter->setData(['isDeleted' => 'test']);
        $this->assertFalse($this->inputFilter->isValid());
        $messages = $this->inputFilter->getMessages();
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('isDeleted', $messages);
        $this->assertIsArray($messages['isDeleted']);
        $this->assertArrayHasKey('notInArray', $messages['isDeleted']);
        $this->assertSame(
            Message::DELETE_ACCOUNT,
            $messages['isDeleted']['notInArray']
        );
    }

    public function testWillPassValidation(): void
    {
        $hash = (new Csrf(['session' => new Container()]))->getHash();

        $this->inputFilter->setData(['isDeleted' => (string) User::IS_DELETED_YES, 'userDeleteCsrf' => $hash]);
        $this->assertTrue($this->inputFilter->isValid());
        $this->assertSame(
            (string) User::IS_DELETED_YES,
            $this->inputFilter->getValue('isDeleted')
        );
    }
}
