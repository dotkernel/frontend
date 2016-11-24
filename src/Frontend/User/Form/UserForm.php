<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Form;

use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * Class UserForm
 * @package Dot\Frontend\User\Form
 */
class UserForm extends Form
{
    /** @var  Fieldset */
    protected $userFieldset;

    /** @var  Fieldset */
    protected $detailsFieldset;

    /**
     * UserForm constructor.
     * @param Fieldset $userFieldset
     * @param Fieldset $detailsFieldset
     * @param array $options
     */
    public function __construct(Fieldset $userFieldset, Fieldset $detailsFieldset, array $options = [])
    {
        $this->detailsFieldset = $detailsFieldset;
        $this->userFieldset = $userFieldset;
        parent::__construct('user_form', $options);
    }

    public function init()
    {
        $this->add($this->userFieldset->get('username'));
        $this->detailsFieldset->setName('details');


        //$this->add($detailsFieldset);

        $this->add([
            'type' => 'Csrf',
            'name' => 'update_account_csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 1800,
                    'message' => ''
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