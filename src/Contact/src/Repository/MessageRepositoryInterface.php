<?php

declare(strict_types=1);

namespace Frontend\Contact\Repository;

use Frontend\Contact\Entity\Message;

interface MessageRepositoryInterface
{
    public function saveMessage(Message $message): void;
}
