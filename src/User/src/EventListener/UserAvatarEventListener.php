<?php

declare(strict_types=1);

namespace Frontend\User\EventListener;

use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Frontend\User\Entity\UserAvatar;

/**
 * Class UserAvatarEventListener
 * @package Frontend\User\EventListener
 *
 * @Service
 */
final class UserAvatarEventListener
{
    private readonly array $config;

    /**
     * UserAvatarEventListener constructor.
     *
     * @Inject({
     *     "config"
     * })
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function postLoad(UserAvatar $userAvatar): void
    {
        $this->setAvatarUrl($userAvatar);
    }

    public function postPersist(UserAvatar $userAvatar): void
    {
        $this->setAvatarUrl($userAvatar);
    }

    public function postUpdate(UserAvatar $userAvatar): void
    {
        $this->setAvatarUrl($userAvatar);
    }

    private function setAvatarUrl(UserAvatar $userAvatar): void
    {
        $userAvatar->setUrl(
            sprintf(
                '%s/%s/%s',
                $this->config['uploads']['user']['url'],
                $userAvatar->getUser()->getUuid()->toString(),
                $userAvatar->getName()
            )
        );
    }
}
