<?php

declare(strict_types=1);

namespace Frontend\Contact\Repository;

use Frontend\Contact\Entity\Message;
use Doctrine\ORM\EntityRepository;

/**
 * Class MessageRepository
 * @package Frontend\Contact\Repository
 * @extends EntityRepository<object>
 */
class MessageRepository extends EntityRepository
{
    /**
     * @param Message $message
     * @return void
     */
    public function saveMessage(Message $message): void
    {
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }
}
