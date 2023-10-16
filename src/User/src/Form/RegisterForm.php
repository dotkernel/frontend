<?php

declare(strict_types=1);

namespace Frontend\User\Form;

use Frontend\User\Fieldset\UserDetailFieldset;
use Frontend\User\InputFilter\RegisterInputFilter;
use Frontend\User\InputFilter\UserDetailInputFilter;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterInterface;

class RegisterForm extends Form
{
    protected InputFilterInterface $inputFilter;

    /**
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->init();

        $this->inputFilter = new RegisterInputFilter();
        $this->inputFilter->init();
        $detailsInputFilter = new UserDetailInputFilter();
        $detailsInputFilter->init();
        $this->inputFilter->add($detailsInputFilter, 'detail');
    }

    public function init()
    {
        parent::init();

        $this->add([
            'name' => 'detail',
            'type' => UserDetailFieldset::class,
        ]);

        $this->add([
            'name'       => 'email',
            'options'    => [
                'label' => 'Email address',
            ],
            'attributes' => [
                'placeholder' => 'Email...',
            ],
            'type'       => Email::class,
        ]);

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
                'value' => 'Register',
            ],
            'type'       => Submit::class,
        ]);
    }

    public function getInputFilter(): InputFilterInterface
    {
        return $this->inputFilter;
    }
}
