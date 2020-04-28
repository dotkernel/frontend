<?php

declare(strict_types=1);

namespace Frontend\Contact\Repository;

use Frontend\Contact\Entity\Message;
use Doctrine\ORM;

/**
 * Class MessageRepositoryInterface
 * @package Frontend\Contact\Repository
 */
interface MessageRepositoryInterface
{
    /**
     * @param Message $message
     * @throws ORM\ORMException
     * @throws ORM\OptimisticLockException
     */
    public function saveMessage(Message $message);
}
