<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Frontend\App\Common\AbstractEntity;

#[ORM\Entity]
#[ORM\Table(name: 'user_remember_me')]
#[ORM\HasLifecycleCallbacks]
class UserRememberMe extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'userUuid', referencedColumnName: 'uuid', nullable: false)]
    protected User $user;

    #[ORM\Column(name: 'rememberMeToken', type: 'string', length: 100, unique: true, nullable: false)]
    protected string $rememberMeToken = '';

    #[ORM\Column(name: 'userAgent', type: 'text')]
    protected ?string $userAgent = null;

    #[ORM\Column(name: 'expireDate', type: 'datetime_immutable')]
    protected DateTimeImmutable $expireDate;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRememberMeToken(): string
    {
        return $this->rememberMeToken;
    }

    public function setRememberMeToken(string $rememberMeToken): self
    {
        $this->rememberMeToken = $rememberMeToken;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getExpireDate(): DateTimeImmutable
    {
        return $this->expireDate;
    }

    public function setExpireDate(DateTimeImmutable $expireDate): self
    {
        $this->expireDate = $expireDate;

        return $this;
    }

    public function getArrayCopy(): array
    {
        return [
            'uuid'       => $this->getUuid()->toString(),
            'name'       => $this->getUser(),
            'userHash'   => $this->getRememberMeToken(),
            'userAgent'  => $this->getUserAgent(),
            'expireDate' => $this->getExpireDate(),
            'created'    => $this->getCreated(),
            'updated'    => $this->getUpdated(),
        ];
    }
}
