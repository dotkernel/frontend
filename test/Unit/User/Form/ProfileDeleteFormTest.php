<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\Form;

use Frontend\User\Form\ProfileDeleteForm;
use FrontendTest\Common\FormTrait;
use PHPUnit\Framework\TestCase;

class ProfileDeleteFormTest extends TestCase
{
    use FormTrait;

    public function testFormWillInstantiate(): void
    {
        $this->formWillInstantiate(ProfileDeleteForm::class);
    }

    public function testFormHasElements(): void
    {
        $this->formHasElements(new ProfileDeleteForm(), [
            'isDeleted',
            'submit',
        ]);
    }

    public function testFormHasInputFilter(): void
    {
        $this->formHasInputFilter((new ProfileDeleteForm())->getInputFilter(), [
            'isDeleted',
        ]);
    }
}