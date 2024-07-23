<?php

declare(strict_types=1);

namespace Frontend\User\EventListener;

use Dot\DependencyInjection\Attribute\Inject;
use Frontend\User\Entity\UserAvatar;

use function sprintf;

class UserAvatarEventListener
{
    #[Inject("config")]
    public function __construct(protected array $config = [])
    {
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
