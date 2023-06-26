<?php

declare(strict_types=1);

namespace Frontend\User\Form;

use Frontend\User\InputFilter\RequestResetPasswordInputFilter;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterInterface;

/**
 * Class RequestResetPasswordForm
 * @package Frontend\User\Form
 */
class RequestResetPasswordForm extends Form
{
    protected InputFilterInterface $inputFilter;

    /**
     * RequestResetPasswordForm constructor.
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->init();

        $this->inputFilter = new RequestResetPasswordInputFilter();
        $this->inputFilter->init();
    }

    public function init()
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
            'name' => 'request_reset_password_csrf',
            'type' => 'csrf',
            'options' => [
                'timeout' => 3600,
                'message' => 'The form CSRF has expired and was refreshed. Please resend the form',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Request'
            ],
            'type' => Submit::class
        ]);
    }

    /**
     * @return InputFilterInterface
     */
    public function getInputFilter(): InputFilterInterface
    {
        return $this->inputFilter;
    }
}
