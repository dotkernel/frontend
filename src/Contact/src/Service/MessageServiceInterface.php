<?php

declare(strict_types=1);

namespace Frontend\Contact\Service;

use Doctrine\ORM\EntityRepository;
use Frontend\Contact\Entity\Message;
use Frontend\Contact\Repository\MessageRepository;

interface MessageServiceInterface
{
    public function getRepository(): MessageRepository|EntityRepository;

    public function processMessage(array $data): bool;

    public function sendContactMail(Message $message): bool;
}
