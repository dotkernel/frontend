<?php

declare(strict_types=1);

namespace Frontend\User\Form;

use Frontend\User\InputFilter\RequestResetPasswordInputFilter;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\Form\FormInterface;
use Laminas\InputFilter\InputFilterInterface;

/** @template-extends Form<FormInterface> */
class RequestResetPasswordForm extends Form
{
    protected InputFilterInterface $inputFilter;

    public function __construct(mixed $name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->init();

        $this->inputFilter = new RequestResetPasswordInputFilter();
        $this->inputFilter->init();
    }

    public function init(): void
    {
        parent::init();

        $this->add([
            'name'       => 'identity',
            'options'    => [
                'label' => 'Email address',
            ],
            'attributes' => [
                'placeholder' => 'Email address',
            ],
            'type'       => Email::class,
        ]);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Request',
            ],
            'type'       => Submit::class,
        ]);
    }

    public function getInputFilter(): InputFilterInterface
    {
        return $this->inputFilter;
    }
}
