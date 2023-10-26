<?php

declare(strict_types=1);

namespace Frontend\User\Form;

use Frontend\User\InputFilter\ProfilePasswordInputFilter;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterInterface;

class ProfilePasswordForm extends Form
{
    protected InputFilterInterface $inputFilter;

    public function __construct(mixed $name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->init();

        $this->inputFilter = new ProfilePasswordInputFilter();
        $this->inputFilter->init();
    }

    public function init(): void
    {
        parent::init();

        $this->add([
            'name'       => 'password',
            'options'    => [
                'label' => 'Password',
            ],
            'attributes' => [
                'placeholder' => 'Password...',
            ],
            'type'       => Password::class,
        ]);

        $this->add([
            'name'       => 'passwordConfirm',
            'options'    => [
                'label' => 'Confirm password',
            ],
            'attributes' => [
                'placeholder' => 'Confirm password...',
            ],
            'type'       => Password::class,
        ]);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Change',
            ],
            'type'       => Submit::class,
        ]);
    }

    public function getInputFilter(): InputFilterInterface
    {
        return $this->inputFilter;
    }
}
