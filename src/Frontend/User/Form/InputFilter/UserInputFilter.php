<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Form\InputFilter;

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
                            'message' => 'Username is required and cannot be empty'
                        ]
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 255,
                            'message' => 'Username invalid length - must be between 3 and 255 characters'
                        ]
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[a-zA-Z0-9-_]+$/',
                            'message' => 'Username invalid characters - only digits, letters and underscore allowed'
                        ]
                    ],
                ],
            ]);
        }

        $this->add($this->userDetailsInputFilter, 'details');
    }
}