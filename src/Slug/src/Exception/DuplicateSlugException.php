<?php

declare(strict_types=1);

namespace Frontend\Slug\Exception;

use Exception;
use Mezzio\Exception\ExceptionInterface;

/**
 * Class DuplicateSlugException
 * @package Frontend\Slug\Exception
 */
class DuplicateSlugException extends Exception implements
    ExceptionInterface
{
}
