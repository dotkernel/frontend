<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\Form;

use Frontend\User\Form\ProfileDetailsForm;
use FrontendTest\Common\FormTrait;
use PHPUnit\Framework\TestCase;

class ProfileDetailsFormTest extends TestCase
{
    use FormTrait;

    public function testFormWillInstantiate(): void
    {
        $this->formWillInstantiate(ProfileDetailsForm::class);
    }

    public function testFormHasElements(): void
    {
        $this->formHasElements(new ProfileDetailsForm(), [
            'detail',
            'submit',
        ]);
    }

    public function testFormHasInputFilter(): void
    {
        $this->formHasInputFilter((new ProfileDetailsForm())->getInputFilter(), [
            'detail' => [
                'firstName',
                'lastName',
            ],
        ]);
    }
}
