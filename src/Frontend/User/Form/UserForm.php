<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Form;

use Zend\Form\Form;

/**
 * Class UserForm
 * @package Dot\Frontend\User\Form
 */
class UserForm extends Form
{
    /**
     * UserForm constructor.
     * @param string $name
     * @param array $options
     */
    public function __construct($name = 'user', array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
        $this->add([
            'name' => 'username',
            'type' => 'text',
            'options' => [
                'label' => 'Username'
            ],
            'attributes' => [
                'placeholder' => 'Username'
            ]
        ]);

        $detailsFieldset = new UserDetailsFieldset();
        $detailsFieldset->setName('details');

        $this->add($detailsFieldset);

        $this->add([
            'type' => 'Csrf',
            'name' => 'update_account_csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 1800,
                    'message' => 'This form has expired. Please refresh page and try again'
                ]
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Save'
            ],
            ['priority' => -100]
        ]);
    }
}