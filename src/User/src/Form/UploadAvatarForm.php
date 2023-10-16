<?php

declare(strict_types=1);

namespace Frontend\User\Form;

use Frontend\User\Fieldset\AvatarFieldset;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterInterface;

class UploadAvatarForm extends Form
{
    protected InputFilterInterface $inputFilter;

    /**
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->init();
    }

    public function init()
    {
        parent::init();

        $this->add([
            'name' => 'avatar',
            'type' => AvatarFieldset::class,
        ]);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Upload',
            ],
            'type'       => Submit::class,
        ]);
    }
}
