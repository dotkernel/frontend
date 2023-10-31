<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Frontend\App\Common\AbstractEntity;
use Frontend\User\EventListener\UserAvatarEventListener;
use Frontend\User\Repository\UserAvatarRepository;

#[ORM\Entity(repositoryClass: UserAvatarRepository::class)]
#[ORM\Table(name: 'user_avatar')]
#[ORM\HasLifecycleCallbacks]
#[ORM\EntityListeners([UserAvatarEventListener::class])]
class UserAvatar extends AbstractEntity
{
    #[ORM\OneToOne(inversedBy: 'avatar', targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'userUuid', referencedColumnName: 'uuid', nullable: false)]
    protected UserInterface $user;

    #[ORM\Column(name: 'name', type: 'string', length: 191)]
    protected string $name;

    protected string $url;

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getArrayCopy(): array
    {
        return [
            'uuid'    => $this->getUuid()->toString(),
            'name'    => $this->getName(),
            'url'     => $this->getUrl(),
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated(),
        ];
    }
}
