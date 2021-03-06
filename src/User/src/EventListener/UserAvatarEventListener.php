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
    /** @var array $config */
    protected $config;

    /**
     * UserAvatarEventListener constructor.
     * @param array $config
     *
     * @Inject({"config"})
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @param UserAvatar $avatar
     */
    public function postLoad(UserAvatar $avatar)
    {
        $this->setAvatarUrl($avatar);
    }

    /**
     * @param UserAvatar $avatar
     */
    public function postPersist(UserAvatar $avatar)
    {
        $this->setAvatarUrl($avatar);
    }

    /**
     * @param UserAvatar $avatar
     */
    public function postUpdate(UserAvatar $avatar)
    {
        $this->setAvatarUrl($avatar);
    }

    /**
     * @param UserAvatar $avatar
     */
    private function setAvatarUrl(UserAvatar $avatar)
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
