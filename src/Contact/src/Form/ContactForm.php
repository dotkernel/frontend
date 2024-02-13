<?php

declare(strict_types=1);

namespace Frontend\Contact\Form;

use Frontend\Contact\InputFilter\ContactInputFilter;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Form;
use Laminas\Form\FormInterface;
use Laminas\InputFilter\InputFilterInterface;

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

        $this->add([
            'name'       => 'email',
            'options'    => [
                'label' => 'E-mail',
            ],
            'attributes' => [
                'placeholder' => 'E-mail address...',
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
            ],
            'type'       => Textarea::class,
        ]);
    }

    public function getInputFilter(): InputFilterInterface
    {
        return $this->inputFilter;
    }
}
