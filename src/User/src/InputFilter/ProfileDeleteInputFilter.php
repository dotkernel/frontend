<?php

/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\User\InputFilter;

use Frontend\App\Common\Message;
use Frontend\User\Entity\User;
use Laminas\InputFilter\InputFilter;

/**
 * Class ProfileDeleteInputFilter
 * @package Frontend\User\InputFilter
 */
class ProfileDeleteInputFilter extends InputFilter
{
    public function init()
    {
        parent::init();

        $this->add([
            'name' => 'isDeleted',
            'required' => true,
            'validators' => [
                [
                    'name' => 'InArray',
                    'options' => [
                        'haystack' => User::IS_DELETED,
                        'message' => Message::DELETE_ACCOUNT,
                    ],
                ],
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => Message::DELETE_ACCOUNT
                    ]
                ],
            ]
        ]);
    }
}
