<?php

declare(strict_types=1);

namespace Frontend\User\Form;

use Frontend\User\Fieldset\AvatarFieldset;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\Form\FormInterface;
use Laminas\InputFilter\InputFilterInterface;

/** @template-extends Form<FormInterface> */
class UploadAvatarForm extends Form
{
    protected InputFilterInterface $inputFilter;

    public function __construct(mixed $name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->init();
    }

    public function init(): void
    {
        parent::init();

        $this->add([
            'name' => 'avatar',
            'type' => AvatarFieldset::class,
        ]);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Upload',
            ],
            'type'       => Submit::class,
        ]);
    }
}
