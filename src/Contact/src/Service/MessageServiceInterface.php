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
    public function getRepository(): MessageRepository;

    public function processMessage(array $data): bool;

    /**
     * @throws MailException
     */
    public function sendContactMail(Message $message): bool;

    /**
     * @param $response
     */
    public function recaptchaIsValid($response): bool;
}
