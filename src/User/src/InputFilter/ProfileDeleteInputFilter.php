<?php

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
final class ProfileDeleteInputFilter extends InputFilter
{
    public function init(): void
    {
        parent::init();

        $input = new Input('isDeleted');
        $input->setRequired(true);
        $input->getValidatorChain()
            ->attachByName(InArray::class, [
                'haystack' => User::IS_DELETED,
                'message' => Message::DELETE_ACCOUNT,
            ], true)
            ->attachByName(NotEmpty::class, [
                'message' => Message::DELETE_ACCOUNT,
            ], true);
        $this->add($input);
    }
}
