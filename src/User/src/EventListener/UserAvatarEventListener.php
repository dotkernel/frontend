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
class UserAvatarEventListener
{
    protected array $config;

    /**
     * UserAvatarEventListener constructor.
     * @param array $config
     *
     * @Inject({
     *     "config"
     * })
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @param UserAvatar $avatar
     * @return void
     */
    public function postLoad(UserAvatar $avatar): void
    {
        $this->setAvatarUrl($avatar);
    }

    /**
     * @param UserAvatar $avatar
     * @return void
     */
    public function postPersist(UserAvatar $avatar): void
    {
        $this->setAvatarUrl($avatar);
    }

    /**
     * @param UserAvatar $avatar
     * @return void
     */
    public function postUpdate(UserAvatar $avatar): void
    {
        $this->setAvatarUrl($avatar);
    }

    /**
     * @param UserAvatar $avatar
     * @return void
     */
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
