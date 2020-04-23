<?php

declare(strict_types=1);

namespace Frontend\Contact\Repository;

use Frontend\Contact\Entity\Message;
use Doctrine\ORM;
use Doctrine\ORM\EntityRepository;

/**
 * Class MessageRepository
 * @package Frontend\Contact\Repository
 */
class MessageRepository extends EntityRepository
{
    /**
     * @param Message $message
     * @throws ORM\ORMException
     * @throws ORM\OptimisticLockException
     */
    public function saveMessage(Message $message)
    {
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }
}
