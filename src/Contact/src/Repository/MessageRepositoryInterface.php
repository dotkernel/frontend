<?php

declare(strict_types=1);

namespace Frontend\Contact\Repository;

use Frontend\Contact\Entity\Message;

/**
 * Interface MessageRepositoryInterface
 * @package Frontend\Contact\Repository
 */
interface MessageRepositoryInterface
{
    /**
     * @param Message $message
     * @return void
     */
    public function saveMessage(Message $message): void;
}
