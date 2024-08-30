<?php

declare(strict_types=1);

namespace Frontend\Contact\Form;

use Fig\Http\Message\RequestMethodInterface;
use Frontend\Contact\InputFilter\ContactInputFilter;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Form;
use Laminas\Form\FormInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Session\Container;

/** @template-extends Form<FormInterface> */
class ContactForm extends Form
{
    protected InputFilterInterface $inputFilter;

    public function __construct(mixed $name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->init();

        $this->inputFilter = new ContactInputFilter();
        $this->inputFilter->init();
    }

    public function init(): void
    {
        parent::init();

        $this->setAttribute('method', RequestMethodInterface::METHOD_POST);

        $this->add([
            'name'       => 'email',
            'options'    => [
                'label' => 'E-mail',
            ],
            'attributes' => [
                'placeholder' => 'E-mail address...',
                'class'       => 'form-control',
            ],
            'type'       => Email::class,
        ]);

        $this->add([
            'name'       => 'name',
            'options'    => [
                'label' => 'Name',
            ],
            'attributes' => [
                'placeholder' => 'Your name...',
                'class'       => 'form-control',
            ],
            'type'       => Text::class,
        ]);

        $this->add([
            'name'       => 'subject',
            'options'    => [
                'label' => 'Subject',
            ],
            'attributes' => [
                'placeholder' => 'Subject...',
                'class'       => 'form-control',
            ],
            'type'       => Hidden::class,
        ]);

        $this->add([
            'name'       => 'message',
            'options'    => [
                'label' => 'Message',
            ],
            'attributes' => [
                'id'          => 'userMessage_textarea',
                'placeholder' => 'Message...',
                'rows'        => 5,
                'class'       => 'form-control',
            ],
            'type'       => Textarea::class,
        ]);

        $this->add(new Csrf('contactCsrf', [
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
