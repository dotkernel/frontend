<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Form;

use Dot\User\Options\UserOptions;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * Class UserForm
 * @package Dot\Frontend\User\Form
 */
class UserForm extends Form
{
    /** @var  UserOptions */
    protected $userOptions;

    /** @var  Fieldset */
    protected $userFieldset;

    /** @var  InputFilter */
    protected $userInputFilter;

    /** @var  Fieldset */
    protected $detailsFieldset;

    /** @var  InputFilter */
    protected $detailsInputFilter;

    /**
     * UserForm constructor.
     * @param Fieldset $userFieldset
     * @param InputFilter $userInputFilter
     * @param Fieldset $detailsFieldset
     * @param InputFilter $detailsInputFilter
     * @param UserOptions $userOptions
     * @param array $options
     */
    public function __construct(
        Fieldset $userFieldset,
        InputFilter $userInputFilter,
        Fieldset $detailsFieldset,
        InputFilter $detailsInputFilter,
        UserOptions $userOptions,
        array $options = [])
    {
        $this->detailsFieldset = $detailsFieldset;
        $this->detailsInputFilter = $detailsInputFilter;
        $this->userFieldset = $userFieldset;
        $this->userInputFilter = $userInputFilter;
        $this->userOptions = $userOptions;

        parent::__construct('user_form', $options);
    }

    public function init()
    {
        $this->userFieldset->setName('user');
        $this->userFieldset->setUseAsBaseFieldset(true);

        //remove some user fields, we don't allow users to update those
        $this->userFieldset->remove('email')->remove('password')->remove('passwordVerify');

        $this->detailsFieldset->setName('details');
        $this->userFieldset->add($this->detailsFieldset);

        $this->userInputFilter->remove('email')->remove('password')->remove('passwordVerify');
        $this->userInputFilter->add($this->detailsInputFilter, 'details');

        if(!$this->userOptions->getRegisterOptions()->isEnableUsername()) {
            $this->userFieldset->remove('username');
            $this->userInputFilter->remove('username');
        }

        $this->add($this->userFieldset);
        $this->getInputFilter()->add($this->userInputFilter, 'user');

        $this->add([
            'type' => 'Csrf',
            'name' => 'update_account_csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 1800,
                    'message' => 'The form used to make the request has expired. Please try again now'
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