<?php

declare(strict_types=1);

namespace FrontendTest\Unit\Contact\Form;

use Frontend\Contact\Form\ContactForm;
use FrontendTest\Common\FormTrait;
use PHPUnit\Framework\TestCase;

class ContactFormTest extends TestCase
{
    use FormTrait;

    public function testFormWillInstantiate(): void
    {
        $this->formWillInstantiate(ContactForm::class);
    }

    public function testFormHasElements(): void
    {
        $this->formHasElements(new ContactForm(), [
            'email',
            'name',
            'subject',
            'message',
        ]);
    }

    public function testFormHasInputFilter(): void
    {
        $this->formHasInputFilter((new ContactForm())->getInputFilter(), [
            'email',
            'name',
            'subject',
            'message',
        ]);
    }
}