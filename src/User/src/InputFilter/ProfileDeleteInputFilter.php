<?php

declare(strict_types=1);

namespace Frontend\User\InputFilter;

use Frontend\App\Common\Message;
use Frontend\User\Entity\User;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Session\Container;
use Laminas\Session\Validator\Csrf;
use Laminas\Validator\InArray;
use Laminas\Validator\NotEmpty;

/**
 * @template TFilteredValues
 * @extends InputFilter<TFilteredValues>
 */
class ProfileDeleteInputFilter extends InputFilter
{
    public function init(): void
    {
        parent::init();

        $isDeleted = new Input('isDeleted');
        $isDeleted->setRequired(true);
        $isDeleted->getValidatorChain()
            ->attachByName(InArray::class, [
                'haystack' => User::IS_DELETED,
                'message'  => Message::DELETE_ACCOUNT,
                'strict'   => InArray::COMPARE_NOT_STRICT,
            ], true)
            ->attachByName(NotEmpty::class, [
                'message' => Message::DELETE_ACCOUNT,
            ], true);
        $this->add($isDeleted);

        $csrf = new Input('userDeleteCsrf');
        $csrf->setRequired(true);
        $csrf->getFilterChain()
            ->attachByName(StringTrim::class)
            ->attachByName(StripTags::class);
        $csrf->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>CSRF</b> is required and cannot be empty',
            ], true)
            ->attachByName(Csrf::class, [
                'name'    => 'userDeleteCsrf',
                'message' => '<b>CSRF</b> is invalid',
                'session' => new Container(),
            ], true);
        $this->add($csrf);
    }
}
