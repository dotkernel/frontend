<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\Form;

use Frontend\User\Form\ProfilePasswordForm;
use FrontendTest\Common\FormTrait;
use PHPUnit\Framework\TestCase;

class ProfilePasswordFormTest extends TestCase
{
    use FormTrait;

    public function testFormWillInstantiate(): void
    {
        $this->formWillInstantiate(ProfilePasswordForm::class);
    }

    public function testFormHasElements(): void
    {
        $this->formHasElements(new ProfilePasswordForm(), [
            'password',
            'passwordConfirm',
            'submit',
        ]);
    }

    public function testFormHasInputFilter(): void
    {
        $this->formHasInputFilter((new ProfilePasswordForm())->getInputFilter(), [
            'password',
            'passwordConfirm',
        ]);
    }
}