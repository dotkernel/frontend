<?php

declare(strict_types=1);

namespace Frontend\User\InputFilter;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\File\IsImage;
use Laminas\Validator\File\UploadFile;
use Laminas\Validator\NotEmpty;

/**
 * @template TFilteredValues
 * @extends InputFilter<TFilteredValues>
 */
class UploadAvatarInputFilter extends InputFilter
{
    public function init(): void
    {
        parent::init();

        $password = new Input('image');
        $password->setRequired(true);
        $password->getFilterChain()
            ->attachByName(StringTrim::class)
            ->attachByName(StripTags::class);
        $password->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => '<b>Image</b> is required and cannot be empty',
            ], true)
            ->attachByName(UploadFile::class, [
                'message' => '<b>Image</b> must be uploaded',
            ], true)
            ->attachByName(IsImage::class, [
                'message' => '<b>Image</b> file must be of type image (*.jpg, *.png)',
            ], true);
        $this->add($password);
    }
}
