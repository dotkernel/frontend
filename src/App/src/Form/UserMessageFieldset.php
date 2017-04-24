<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\App\Form;

use Dot\Hydrator\ClassMethodsCamelCase;
use Frontend\App\Entity\UserMessageEntity;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Class UserMessageFieldset
 * @package Frontend\App\Form
 */
class UserMessageFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('userMessage');

        $this->setObject(new UserMessageEntity());
        $this->setHydrator(new ClassMethodsCamelCase());
    }

    public function init()
    {
        $this->add([
            'name' => 'email',
            'type' => 'text',
            'options' => [
                'label' => 'E-mail'
            ],
            'attributes' => [
                'placeholder' => 'E-mail address...'
            ]
        ]);

        $this->add([
            'name' => 'name',
            'type' => 'text',
            'options' => [
                'label' => 'Name'
            ],
            'attributes' => [
                'placeholder' => 'Your name...'
            ]
        ]);

        $this->add([
            'name' => 'subject',
            'type' => 'text',
            'options' => [
                'label' => 'Subject'
            ],
            'attributes' => [
                'placeholder' => 'Subject...'
            ]
        ]);

        $this->add([
            'name' => 'message',
            'type' => 'textarea',
            'options' => [
                'label' => 'Message'
            ],
            'attributes' => [
                'id' => 'userMessage_textarea',
                'placeholder' => 'Message...',
                'rows' => 5,
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'email' => [
                'filters' => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => '<b>E-mail</b> address is required and cannot be empty',
                        ]
                    ],
                    [
                        'name' => 'EmailAddress',
                        'options' => [
                            'message' => '<b>E-mail</b> address is invalid',
                        ]
                    ],
                ],
            ],
            'name' => [
                'filters' => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => '<b>Name</b> is required and cannot be empty',
                        ]
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'max' => 500
                        ]
                    ]
                ],
            ],
            'subject' => [
                'filters' => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => '<b>Subject</b> is required and cannot be empty',
                        ]
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'max' => 500
                        ]
                    ]
                ],
            ],
            'message' => [
                'filters' => [],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => '<b>Message</b> is required and cannot be empty',
                        ]
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'max' => 1000
                        ]
                    ]
                ],
            ],
        ];
    }
}
