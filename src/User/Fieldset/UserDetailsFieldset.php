<?php
/**
 * @copyright: DotKernel
 * @library: dot-frontend
 * @author: n3vrax
 * Date: 2/23/2017
 * Time: 1:41 AM
 */

declare(strict_types = 1);

namespace App\User\Fieldset;

use App\User\Entity\UserDetailsEntity;
use Dot\Hydrator\ClassMethodsCamelCase;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Class UserDetailsFieldset
 * @package App\User\Fieldset
 */
class UserDetailsFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * UserDetailsFieldset constructor.
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null, array $options = [])
    {
        parent::__construct('details', $options);
        $this->setObject(new UserDetailsEntity());
        $this->setHydrator(new ClassMethodsCamelCase());
    }

    public function init()
    {
        $this->add([
            'name' => 'firstName',
            'type' => 'text',
            'options' => [
                'label' => 'First name'
            ],
            'attributes' => [
                'placeholder' => 'First name...'
            ]
        ]);
        $this->add([
            'name' => 'lastName',
            'type' => 'text',
            'options' => [
                'label' => 'Last name'
            ],
            'attributes' => [
                'placeholder' => 'Last name...'
            ]
        ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'firstName' => [
                'filters' => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => 'First name is required and cannot be empty'
                        ]
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'max' => 150,
                            'message' => 'First name character limit of 150 exceeded',
                        ],
                    ]
                ]
            ],
            'lastName' => [
                'filters' => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => 'Last name is required and cannot be empty'
                        ]
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'max' => 150,
                            'message' => 'Last name character limit of 150 exceeded',
                        ],
                    ]
                ]
            ],
        ];
    }
}
