<?php

declare(strict_types=1);

namespace Frontend\User\Form;

use Frontend\User\Fieldset\UserDetailFieldset;
use Frontend\User\InputFilter\ProfileDetailsInputFilter;
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
class ProfileDetailsForm extends Form
{
    protected InputFilterInterface $inputFilter;

    public function __construct(mixed $name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->init();

        $this->inputFilter = new InputFilter();

        $csrf = new Input('userDetailsCsrf');
        $csrf->setRequired(true);
        $csrf->getFilterChain()
            ->attachByName(StringTrim::class)
            ->attachByName(StripTags::class);
        $csrf->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>CSRF</b> is required and cannot be empty',
            ], true)
            ->attachByName(\Laminas\Session\Validator\Csrf::class, [
                'name'    => 'userDetailsCsrf',
                'message' => '<b>CSRF</b> is invalid',
                'session' => new Container(),
            ], true);
        $this->inputFilter->add($csrf);

        $detailsInputFilter = new ProfileDetailsInputFilter();
        $detailsInputFilter->init();

        $this->inputFilter->add($detailsInputFilter, 'detail');
    }

    public function init(): void
    {
        parent::init();

        $this->add([
            'name' => 'detail',
            'type' => UserDetailFieldset::class,
        ]);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Update',
            ],
            'type'       => Submit::class,
        ]);

        $this->add(new Csrf('userDetailsCsrf', [
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
