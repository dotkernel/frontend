<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\App\Service;

use Frontend\App\Entity\UserMessageEntity;

/**
 * Interface UserMessageServiceInterface
 * @package Frontend\App\Service
 */
interface UserMessageServiceInterface
{
    /**
     * @param UserMessageEntity $message
     * @param array $options
     */
    public function save(UserMessageEntity $message, array $options = []);
}
