<?php

declare(strict_types=1);

namespace Frontend\Contact\Service;

use Dot\Mail\Exception\MailException;
use Frontend\Contact\Repository\MessageRepository;

/**
 * Interface MessageService
 * @package Frontend\Contact\Service
 */
interface MessageServiceInterface
{
    /**
     * @return MessageRepository
     */
    public function getRepository(): MessageRepository;

    /**
     * @param array $data
     * @return bool
     * @throws MailException
     */
    public function processMessage(array $data): bool;
}
