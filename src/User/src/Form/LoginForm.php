<?php

declare(strict_types=1);

namespace Frontend\User\Form;

use Frontend\User\InputFilter\LoginInputFilter;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterInterface;

/**
 * Class LoginForm
 * @package Frontend\User\Form
 */
final class LoginForm extends Form
{
    private readonly InputFilterInterface $inputFilter;

    /**
     * LoginForm constructor.
     * @param null $name
     */
    public function __construct($name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->init();

        $this->inputFilter = new LoginInputFilter();
        $this->inputFilter->init();
    }

    public function init(): void
    {
        parent::init();

        $this->add([
            'name' => 'identity',
            'options' => [
                'label' => 'Email address'
            ],
            'attributes' => [
                'placeholder' => 'Email address',
            ],
            'type' => Email::class
        ]);

        $this->add([
            'name' => 'password',
            'options' => [
                'label' => 'Password'
            ],
            'attributes' => [
                'placeholder' => 'Password',
            ],
            'type' => Password::class
        ]);

        $this->add([
            'name' => 'rememberMe',
            'type' => 'checkbox',
            'attributes' => [
                'class' => 'tooltips',
                'data-toggle' => 'tooltip',
                'title' => 'Remember me',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Log in'
            ],
            'type' => Submit::class
        ]);
    }

    public function getInputFilter(): InputFilterInterface
    {
        return $this->inputFilter;
    }
}
