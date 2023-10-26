<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\InputFilter;

use Frontend\App\Common\Message;
use Frontend\User\InputFilter\ProfileDeleteInputFilter;
use FrontendTest\Common\AbstractInputFilterTest;

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
        $this->inputFilter->setData(['isDeleted' => true]);
        $this->assertTrue($this->inputFilter->isValid());
        $this->assertSame(
            true,
            $this->inputFilter->getValue('isDeleted')
        );
    }
}
