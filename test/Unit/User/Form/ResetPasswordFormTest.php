<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\Form;

use Frontend\User\Form\ResetPasswordForm;
use FrontendTest\Common\FormTrait;
use PHPUnit\Framework\TestCase;

class ResetPasswordFormTest extends TestCase
{
    use FormTrait;

    public function testFormWillInstantiate(): void
    {
        $this->formWillInstantiate(ResetPasswordForm::class);
    }

    public function testFormHasElements(): void
    {
        $this->formHasElements(new ResetPasswordForm(), [
            'password',
            'passwordConfirm',
            'submit',
        ]);
    }

    public function testFormHasInputFilter(): void
    {
        $this->formHasInputFilter((new ResetPasswordForm())->getInputFilter(), [
            'password',
            'passwordConfirm',
        ]);
    }
}