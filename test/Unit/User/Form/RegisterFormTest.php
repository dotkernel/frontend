<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\Form;

use Frontend\User\Form\RegisterForm;
use FrontendTest\Common\FormTrait;
use PHPUnit\Framework\TestCase;

class RegisterFormTest extends TestCase
{
    use FormTrait;

    public function testFormWillInstantiate(): void
    {
        $this->formWillInstantiate(RegisterForm::class);
    }

    public function testFormHasElements(): void
    {
        $this->formHasElements(new RegisterForm(), [
            'detail',
            'email',
            'password',
            'passwordConfirm',
            'submit',
        ]);
    }

    public function testFormHasInputFilter(): void
    {
        $this->formHasInputFilter((new RegisterForm())->getInputFilter(), [
            'detail' => [
                'firstName',
                'lastName',
            ],
            'email',
            'password',
            'passwordConfirm',
        ]);
    }
}
