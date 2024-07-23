<?php

declare(strict_types=1);

namespace Frontend\Contact\Repository;

use Doctrine\ORM\EntityRepository;
use Dot\DependencyInjection\Attribute\Entity;
use Frontend\Contact\Entity\Message;

/**
 * @extends EntityRepository<object>
 */
#[Entity(Message::class)]
class MessageRepository extends EntityRepository implements MessageRepositoryInterface
{
    public function saveMessage(Message $message): Message
    {
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();

        return $message;
    }
}
