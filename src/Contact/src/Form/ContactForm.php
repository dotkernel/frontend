<?php

declare(strict_types=1);

namespace Frontend\Contact\Form;

use Frontend\Contact\InputFilter\ContactInputFilter;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

/**
 * Class ContactForm
 * @package Frontend\Contact\Form
 */
class ContactForm extends Form
{
    /** @var InputFilter $inputFilter */
    protected $inputFilter;

    /**
     * ContactForm constructor.
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->init();

        $this->inputFilter = new ContactInputFilter();
        $this->inputFilter->init();
    }

    public function init()
    {
        parent::init();

        $this->add([
            'name' => 'email',
            'options' => [
                'label' => 'E-mail'
            ],
            'attributes' => [
                'placeholder' => 'E-mail address...'
            ],
            'type' => Email::class
        ]);

        $this->add([
            'name' => 'name',
            'options' => [
                'label' => 'Name'
            ],
            'attributes' => [
                'placeholder' => 'Your name...'
            ],
            'type' => Text::class,
        ]);

        $this->add([
            'name' => 'subject',
            'options' => [
                'label' => 'Subject'
            ],
            'attributes' => [
                'placeholder' => 'Subject...'
            ],
            'type' => Text::class,
        ]);

        $this->add([
            'name' => 'message',
            'options' => [
                'label' => 'Message'
            ],
            'attributes' => [
                'id' => 'userMessage_textarea',
                'placeholder' => 'Message...',
                'rows' => 5,
            ],
            'type' => Textarea::class,
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Send message'
            ]
        ], ['priority' => -105]);
    }

    /**
     * @return null|InputFilter|\Laminas\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        return $this->inputFilter;
    }
}
