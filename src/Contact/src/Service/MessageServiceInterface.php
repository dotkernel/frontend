<?php

declare(strict_types=1);

namespace Frontend\Contact\Service;

use Dot\Mail\Exception\MailException;
use Frontend\Contact\Entity\Message;
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
     */
    public function processMessage(array $data): bool;

    /**
     * @param Message $message
     * @return bool
     * @throws MailException
     */
    public function sendContactMail(Message $message): bool;

    /**
     * @param $response
     * @return bool
     */
    public function recaptchaIsValid($response): bool;
}
