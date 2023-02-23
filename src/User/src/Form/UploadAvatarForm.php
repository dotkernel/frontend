<?php

declare(strict_types=1);

namespace Frontend\User\Form;

use Frontend\User\Fieldset\AvatarFieldset;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterInterface;

/**
 * Class UploadAvatarForm
 * @package Frontend\User\Form
 */
final class UploadAvatarForm extends Form
{
    /**
     * UploadAvatarForm constructor.
     * @param null $name
     */
    public function __construct($name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->init();
    }

    public function init(): void
    {
        parent::init();

        $this->add([
            'name' => 'avatar',
            'type' => AvatarFieldset::class
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Upload'
            ],
            'type' => Submit::class
        ]);
    }
}
