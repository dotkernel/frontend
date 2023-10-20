<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\Form;

use Frontend\User\Form\RequestResetPasswordForm;
use FrontendTest\Common\FormTrait;
use PHPUnit\Framework\TestCase;

class RequestResetPasswordFormTest extends TestCase
{
    use FormTrait;

    public function testFormWillInstantiate(): void
    {
        $this->formWillInstantiate(RequestResetPasswordForm::class);
    }

    public function testFormHasElements(): void
    {
        $this->formHasElements(new RequestResetPasswordForm(), [
            'identity',
            'submit',
        ]);
    }

    public function testFormHasInputFilter(): void
    {
        $this->formHasInputFilter((new RequestResetPasswordForm())->getInputFilter(), [
            'identity',
        ]);
    }
}
