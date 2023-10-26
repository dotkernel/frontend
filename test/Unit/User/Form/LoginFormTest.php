<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\Form;

use Frontend\User\Form\LoginForm;
use FrontendTest\Common\FormTrait;
use PHPUnit\Framework\TestCase;

class LoginFormTest extends TestCase
{
    use FormTrait;

    public function testFormWillInstantiate(): void
    {
        $this->formWillInstantiate(LoginForm::class);
    }

    public function testFormHasElements(): void
    {
        $this->formHasElements(new LoginForm(), [
            'identity',
            'password',
            'rememberMe',
            'submit',
        ]);
    }

    public function testFormHasInputFilter(): void
    {
        $this->formHasInputFilter((new LoginForm())->getInputFilter(), [
            'identity',
            'password',
            'rememberMe',
        ]);
    }
}
