<?php

declare(strict_types=1);

namespace Frontend\User\Form;

use Fig\Http\Message\RequestMethodInterface;
use Frontend\User\InputFilter\LoginInputFilter;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\Form\FormInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Session\Container;

/** @template-extends Form<FormInterface> */
class LoginForm extends Form
{
    protected InputFilterInterface $inputFilter;

    public function __construct(mixed $name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->init();

        $this->inputFilter = new LoginInputFilter();
        $this->inputFilter->init();
    }

    public function init(): void
    {
        parent::init();

        $this->setAttribute('method', RequestMethodInterface::METHOD_POST);

        $this->add([
            'name'       => 'identity',
            'options'    => [
                'label' => 'Email address',
            ],
            'attributes' => [
                'placeholder' => 'Email address',
                'class'       => 'form-control',
            ],
            'type'       => Email::class,
        ]);

        $this->add([
            'name'       => 'password',
            'options'    => [
                'label' => 'Password',
            ],
            'attributes' => [
                'placeholder' => 'Password',
                'class'       => 'form-control',
            ],
            'type'       => Password::class,
        ]);

        $this->add([
            'name'       => 'rememberMe',
            'type'       => 'checkbox',
            'attributes' => [
                'class'       => 'tooltips',
                'data-toggle' => 'tooltip',
                'title'       => 'Remember me',
                'id'          => 'rememberMe',
            ],
        ]);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Log in',
            ],
            'type'       => Submit::class,
        ]);

        $this->add(new Csrf('userLoginCsrf', [
            'csrf_options' => [
                'timeout' => 3600,
                'session' => new Container(),
            ],
        ]));
    }

    public function getInputFilter(): InputFilterInterface
    {
        return $this->inputFilter;
    }
}
