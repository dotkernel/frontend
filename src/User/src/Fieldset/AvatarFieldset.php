<?php

declare(strict_types=1);

namespace Frontend\User\Fieldset;

use Laminas\Form\Element\File;
use Laminas\Form\Fieldset;

class AvatarFieldset extends Fieldset
{
    /**
     * @param mixed $name
     * @param array $options
     */
    public function __construct($name = null, array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init(): void
    {
        parent::init();

        $this->add([
            'name'       => 'image',
            'attributes' => [
                'class' => 'img-input',
                'name'  => 'image',
            ],
            'type'       => File::class,
        ]);
    }
}
