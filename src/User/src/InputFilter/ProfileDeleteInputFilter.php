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
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\InArray;
use Laminas\Validator\NotEmpty;

/**
 * Class ProfileDeleteInputFilter
 * @package Frontend\User\InputFilter
 */
class ProfileDeleteInputFilter extends InputFilter
{
    public function init()
    {
        parent::init();

        $isDeleted = new Input('isDeleted');
        $isDeleted->setRequired(true);
        $isDeleted->getValidatorChain()
            ->attachByName(InArray::class, [
                'haystack' => User::IS_DELETED,
                'message' => Message::DELETE_ACCOUNT,
            ], true)
            ->attachByName(NotEmpty::class, [
                'message' => Message::DELETE_ACCOUNT,
            ], true);
        $this->add($isDeleted);
    }
}
