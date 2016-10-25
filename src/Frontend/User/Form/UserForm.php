<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Form;

use Dot\Frontend\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Zend\Form\Form;

/**
 * Class UserForm
 * @package Dot\Frontend\User\Form
 */
class UserForm extends Form
{
    /** @var  UserOptions */
    protected $userOptions;

    /**
     * UserForm constructor.
     * @param UserOptions $userOptions
     * @param string $name
     * @param array $options
     */
    public function __construct(UserOptions $userOptions, $name = 'user', array $options = [])
    {
        $this->userOptions = $userOptions;
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
        $detailsFieldset->init();
        $detailsFieldset->setName('details');

        $this->add($detailsFieldset);

        $this->add([
            'type' => 'Csrf',
            'name' => 'update_account_csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 1800,
                    'message' => $this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::MESSAGE_CSRF_EXPIRED)
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