<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Form\InputFilter;

use Dot\Frontend\User\Options\MessagesOptions;
use Dot\Frontend\User\Options\UserOptions;
use Zend\InputFilter\InputFilter;

/**
 * Class UserDetailsInputFilter
 * @package Dot\Frontend\User\Form\InputFilter
 */
class UserDetailsInputFilter extends InputFilter
{
    /** @var  UserOptions */
    protected $userOptions;

    /**
     * UserDetailsInputFilter constructor.
     * @param UserOptions $userOptions
     */
    public function __construct(UserOptions $userOptions)
    {
        $this->userOptions = $userOptions;
    }

    /**
     * initialize intput filter with default validators
     */
    public function init()
    {
        $this->add([
            'name' => 'firstName',
            'filters' => [
                ['name' => 'StringTrim']
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => $this->userOptions->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_FIRST_NAME_EMPTY)
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'max' => 150,
                        'message' => $this->userOptions->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_FIRST_NAME_CHARACTER_LIMIT)
                    ]
                ]
            ],
        ]);

        $this->add([
            'name' => 'lastName',
            'filters' => [
                ['name' => 'StringTrim']
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => $this->userOptions->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_LAST_NAME_EMPTY)
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'max' => 150,
                        'message' => $this->userOptions->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_LAST_NAME_CHARACTER_LIMIT)
                    ]
                ]
            ],
        ]);
    }
}