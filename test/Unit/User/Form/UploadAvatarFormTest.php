<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\Form;

use Frontend\User\Form\UploadAvatarForm;
use FrontendTest\Common\FormTrait;
use PHPUnit\Framework\TestCase;
class UploadAvatarFormTest extends TestCase
{
    use FormTrait;

    public function testFormWillInstantiate(): void
    {
        $this->formWillInstantiate(UploadAvatarForm::class);
    }

    public function testFormHasElements(): void
    {
        $this->formHasElements(new UploadAvatarForm(), [
            'avatar',
            'submit',
        ]);
    }

    public function testFormHasInputFilter(): void
    {
        $this->formHasInputFilter((new UploadAvatarForm())->getInputFilter(), [
            'submit',
            'avatar' => ['image']
        ]);
    }
}