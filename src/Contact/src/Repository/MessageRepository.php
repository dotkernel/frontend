<?php

declare(strict_types=1);

namespace Frontend\Contact\Repository;

use Doctrine\ORM\EntityRepository;
use Dot\AnnotatedServices\Annotation\Entity;
use Frontend\Contact\Entity\Message;

/**
 * @Entity(name="Frontend\Contact\Entity\Message")
 * @extends EntityRepository<object>
 */
class MessageRepository extends EntityRepository
{
    public function saveMessage(Message $message): void
    {
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }
}
