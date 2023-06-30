<?php

declare(strict_types=1);

namespace Frontend\Contact\Service;

use Doctrine\ORM\EntityRepository;
use Dot\Mail\Exception\MailException;
use Frontend\Contact\Entity\Message;
use Frontend\Contact\Repository\MessageRepository;

/**
 * Interface MessageService
 * @package Frontend\Contact\Service
 */
interface MessageServiceInterface
{
    public function getRepository(): MessageRepository|EntityRepository;

    /**
     * @param array $data
     * @return bool
     */
    public function processMessage(array $data): bool;

    /**
     * @param Message $message
     * @return bool
     * @throws MailException
     */
    public function sendContactMail(Message $message): bool;
}
