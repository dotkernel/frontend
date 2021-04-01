<?php

declare(strict_types=1);

namespace Frontend\Slug\Exception;

use Exception;
use Mezzio\Exception\ExceptionInterface;

/**
 * Class MissingConfigurationException
 * @package Frontend\Slug\Exception
 */
class MissingConfigurationException extends Exception implements
    ExceptionInterface
{
}
