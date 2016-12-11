<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Form\InputFilter;

use Zend\InputFilter\InputFilter;

/**
 * Class UserDetailsInputFilter
 * @package Dot\Frontend\User\Form\InputFilter
 */
class UserDetailsInputFilter extends InputFilter
{
    const FIRSTNAME_REQUIRED = 'First name is required and cannot be empty';
    const FIRSTNAME_LIMIT = 'First name cannot have more than 150 characters';

    const LASTNAME_REQUIRED = 'Last name is required and cannot be empty';
    const LASTNAME_LIMIT = 'Last name cannot have more than 150 characters';

    const PHONE_INVALID = 'Phone number is invalid';

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
                        'message' => static::FIRSTNAME_REQUIRED
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'max' => 150,
                        'message' => static::FIRSTNAME_LIMIT
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
                        'message' => static::LASTNAME_REQUIRED
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'max' => 150,
                        'message' => static::LASTNAME_LIMIT
                    ]
                ]
            ],
        ]);

        $this->add([
            'name' => 'address',
            'required' => false,
            'filters' => [
                ['name' => 'StringTrim']
            ],
            'validators' => [],
        ]);

        $this->add([
            'name' => 'phone',
            'required' => false,
            'filters' => [
                ['name' => 'StringTrim']
            ],
            'validators' => [
                [
                    'name' => 'Regex',
                    'options' => [
                        'pattern' => '/^\+?\d+$/',
                        'message' => static::PHONE_INVALID
                    ]
                ],
            ],
        ]);
    }
}