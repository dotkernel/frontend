<?php

declare(strict_types=1);

namespace Frontend\Slug\Exception;

use Mezzio\Exception\ExceptionInterface;
use RuntimeException as PhpRuntimeException;

/**
 * Class RuntimeException
 * @package Frontend\Slug\Exception
 */
class RuntimeException extends PhpRuntimeException implements ExceptionInterface
{
}
