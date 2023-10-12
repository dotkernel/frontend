<?php

declare(strict_types=1);

namespace Frontend\User\Fieldset;

use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;

class UserDetailFieldset extends Fieldset
{
    public function __construct($name = null, array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init(): void
    {
        parent::init();

        $this->add([
            'name' => 'firstName',
            'options' => [
                'label' => 'First Name'
            ],
            'attributes' => [
                'placeholder' => 'First Name...',
            ],
            'type' => Text::class
        ]);

        $this->add([
            'name' => 'lastName',
            'options' => [
                'label' => 'Last Name'
            ],
            'attributes' => [
                'placeholder' => 'Last Name...',
            ],
            'type' => Text::class
        ]);
    }
}
