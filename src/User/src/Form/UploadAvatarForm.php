<?php

declare(strict_types=1);

namespace Frontend\User\Form;

use Frontend\User\Fieldset\AvatarFieldset;
use Frontend\User\InputFilter\UploadAvatarInputFilter;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\Form\FormInterface;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Session\Container;
use Laminas\Validator\NotEmpty;

/** @template-extends Form<FormInterface> */
class UploadAvatarForm extends Form
{
    protected InputFilterInterface $inputFilter;

    public function __construct(mixed $name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->init();

        $this->inputFilter = new InputFilter();

        $csrf = new Input('userAvatarCsrf');
        $csrf->setRequired(true);
        $csrf->getFilterChain()
            ->attachByName(StringTrim::class)
            ->attachByName(StripTags::class);
        $csrf->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>CSRF</b> is required and cannot be empty',
            ], true)
            ->attachByName(\Laminas\Session\Validator\Csrf::class, [
                'name'    => 'userAvatarCsrf',
                'message' => '<b>CSRF</b> is invalid',
                'session' => new Container(),
            ], true);
        $this->inputFilter->add($csrf);

        $avatarInputFilter = new UploadAvatarInputFilter();
        $avatarInputFilter->init();

        $this->inputFilter->add($avatarInputFilter, 'avatar');
    }

    public function init(): void
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

        $this->add(new Csrf('userAvatarCsrf', [
            'csrf_options' => [
                'timeout' => 3600,
                'session' => new Container(),
            ],
        ]));
    }
}
