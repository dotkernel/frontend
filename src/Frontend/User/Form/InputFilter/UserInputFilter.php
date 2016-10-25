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
use Dot\User\Options\UserOptions;
use Zend\InputFilter\InputFilter;

/**
 * Class UserInputFilter
 * @package Dot\Frontend\User\Form\InputFilter
 */
class UserInputFilter extends InputFilter
{
    /** @var  UserOptions */
    protected $userOptions;

    /** @var  InputFilter */
    protected $userDetailsInputFilter;

    /**
     * UserInputFilter constructor.
     * @param UserOptions $userOptions
     * @param InputFilter $userDetailsInputFilter
     */
    public function __construct(
        UserOptions $userOptions,
        InputFilter $userDetailsInputFilter
    ) {
        $this->userOptions = $userOptions;
        $this->userDetailsInputFilter = $userDetailsInputFilter;
    }

    public function init()
    {
        if ($this->userOptions->getRegisterOptions()->isEnableUsername()) {
            $this->add([
                'name' => 'username',
                'filters' => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::MESSAGE_REGISTER_EMPTY_USERNAME)
                        ]
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 255,
                            'message' => $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::MESSAGE_REGISTER_USERNAME_CHARACTER_LIMIT)
                        ]
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[a-zA-Z0-9-_]+$/',
                            'message' => $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::MESSAGE_REGISTER_USERNAME_INVALID_CHARACTERS)
                        ]
                    ],
                ],
            ]);
        }

        $this->add($this->userDetailsInputFilter, 'details');
    }
}