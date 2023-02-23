<?php

declare(strict_types=1);

namespace Frontend\User\Form;

use Frontend\User\Fieldset\UserDetailFieldset;
use Frontend\User\InputFilter\UserDetailInputFilter;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;

/**
 * Class ProfileDetailsForm
 * @package Frontend\User\Form
 */
final class ProfileDetailsForm extends Form
{
    private readonly InputFilterInterface $inputFilter;

    /**
     * ProfileDetailsForm constructor.
     * @param null $name
     */
    public function __construct($name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->init();

        $this->inputFilter = new InputFilter();
        $userDetailInputFilter = new UserDetailInputFilter();
        $userDetailInputFilter->init();

        $this->inputFilter->add($userDetailInputFilter, 'detail');
    }

    public function init(): void
    {
        parent::init();

        $this->add([
            'name' => 'detail',
            'type' => UserDetailFieldset::class
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Update'
            ],
            'type' => Submit::class
        ]);
    }

    public function getInputFilter(): InputFilterInterface
    {
        return $this->inputFilter;
    }
}
