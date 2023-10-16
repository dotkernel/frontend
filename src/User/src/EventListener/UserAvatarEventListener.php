<?php

declare(strict_types=1);

namespace Frontend\User\EventListener;

use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Frontend\User\Entity\UserAvatar;

use function sprintf;

/**
 * @Service
 */
class UserAvatarEventListener
{
    protected array $config;

    /**
     * @param array $config
     * @Inject({
     *     "config"
     * })
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function postLoad(UserAvatar $avatar): void
    {
        $this->setAvatarUrl($avatar);
    }

    public function postPersist(UserAvatar $avatar): void
    {
        $this->setAvatarUrl($avatar);
    }

    public function postUpdate(UserAvatar $avatar): void
    {
        $this->setAvatarUrl($avatar);
    }

    private function setAvatarUrl(UserAvatar $avatar): void
    {
        $avatar->setUrl(
            sprintf(
                '%s/%s/%s',
                $this->config['uploads']['user']['url'],
                $avatar->getUser()->getUuid()->toString(),
                $avatar->getName()
            )
        );
    }
}
