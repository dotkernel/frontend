<?php

declare(strict_types=1);

namespace Frontend\Contact\Repository;

use Frontend\Contact\Entity\Message;
use Doctrine\ORM\EntityRepository;

/**
 * Class MessageRepository
 * @package Frontend\Contact\Repository
 */
final class MessageRepository extends EntityRepository
{
    public function saveMessage(Message $message): void
    {
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }
}
