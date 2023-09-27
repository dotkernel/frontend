<?php

declare(strict_types=1);

namespace Frontend\Contact\Form;

use Frontend\Contact\InputFilter\ContactInputFilter;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterInterface;

/**
 * Class ContactForm
 * @package Frontend\Contact\Form
 */
class ContactForm extends Form
{
    protected InputFilterInterface $inputFilter;

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

    /**
     * @return void
     */
    public function init(): void
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
            'type' => Hidden::class,
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
            'name' => 'contact_csrf',
            'type' => 'csrf',
            'options' => [
                'timeout' => 3600,
                'message' => 'The form CSRF has expired and was refreshed. Please resend the form',
            ],
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
