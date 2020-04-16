<?php

namespace Frontend\Contact\Service;

use Frontend\Contact\Entity\Message;
use Frontend\Contact\Repository\MessageRepository;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Class MessageService
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function processMessage(array $data);
}
