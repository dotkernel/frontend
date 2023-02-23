<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Frontend\App\Common\AbstractEntity;

/**
 * Class UserRememberMe
 * @ORM\Entity()
 * @ORM\Table(name="user_remember_me")
 * @ORM\HasLifecycleCallbacks()
 * @package Frontend\User\Entity
 */
class UserRememberMe extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Frontend\User\Entity\User")
     * @ORM\JoinColumn(name="userUuid", referencedColumnName="uuid", nullable=false)
     */
    protected User $user;

    /**
     * @ORM\Column(name="rememberMeToken", type="string", length=100, nullable=false, unique=true)
     */
    protected string $rememberMeToken = '';

    /**
     * @ORM\Column(name="userAgent", type="text")
     */
    protected ?string $userAgent = null;

    /**
     * @ORM\Column(name="expireDate", type="datetime_immutable")
     */
    protected DateTimeImmutable $expireDate;

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRememberMeToken(): string
    {
        return $this->rememberMeToken;
    }

    /**
     * @return $this
     */
    public function setRememberMeToken(string $rememberMeToken): self
    {
        $this->rememberMeToken = $rememberMeToken;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    /**
     * @return $this
     */
    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getExpireDate(): DateTimeImmutable
    {
        return $this->expireDate;
    }

    /**
     * @return $this
     */
    public function setExpireDate(DateTimeImmutable $expireDate): self
    {
        $this->expireDate = $expireDate;

        return $this;
    }

    /**
     * @return array{uuid: string, name: \Frontend\User\Entity\User, userHash: string, userAgent: string|null, expireDate: \DateTimeImmutable, created: \DateTimeImmutable, updated: \DateTimeImmutable|null}
     */
    public function getArrayCopy(): array
    {
        return [
            'uuid' => $this->getUuid()->toString(),
            'name' => $this->user,
            'userHash' => $this->rememberMeToken,
            'userAgent' => $this->userAgent,
            'expireDate' => $this->expireDate,
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated()
        ];
    }
}
