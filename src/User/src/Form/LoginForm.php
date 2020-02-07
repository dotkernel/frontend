<?php

declare(strict_types=1);

namespace Frontend\User\Form;

use Laminas\Form\Element\Email;
use Laminas\Form\Element\Password;
use Laminas\Form\Form;

/**
 * Class LoginForm
 * @package Frontend\User\Form
 */
class LoginForm extends Form
{
    /**
     * LoginForm constructor.
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'name' => 'identity',
            'options' => [
                'label' => 'Email address'
            ],
            'type' => Email::class
        ])->add([
            'name' => 'password',
            'options' => [
                'label' => 'Password'
            ],
            'type' => Password::class
        ]);
    }
}
