<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/2/2016
 * Time: 9:18 PM
 */

namespace Dot\Frontend\User\Form;

use Zend\Form\Fieldset;

/**
 * Class UserDetailsFieldset
 * @package Dot\Frontend\User\Form
 */
class UserDetailsFieldset extends Fieldset
{
    /**
     * UserDetailsFieldset constructor.
     * @param string $name
     * @param array $options
     */
    public function __construct($name = 'user_details', array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
        $this->add([
            'type' => 'text',
            'name' => 'firstName',
            'options' => [
                'label' => 'First Name',
            ],
            'attributes' => [
                'placeholder' => 'First Name'
            ]
        ]);

        $this->add([
            'type' => 'text',
            'name' => 'lastName',
            'options' => [
                'label' => 'Last Name'
            ],
            'attributes' => [
                'placeholder' => 'Last Name'
            ]
        ]);
    }
}