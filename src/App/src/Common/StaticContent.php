<?php

declare(strict_types=1);

namespace Frontend\App\Common;


/**
 * Class StaticContent
 * @package Frontend\App\Common
 */
class StaticContent
{
    const GENERAL_UUID_REGEX = '[0-9A-Fa-f]{8}-?[0-9A-Fa-f]{4}-?[0-9A-Fa-f]{4}-?[0-9A-Fa-f]{4}-?[0-9A-Fa-f]{12}';

    const UUID_REGEX = '{uuid:' . self::GENERAL_UUID_REGEX . '}';

    const IMAGE_WIDTH = 500;
    const IMAGE_HEIGHT = 500;
}
