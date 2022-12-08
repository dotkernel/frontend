<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

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
     * @var User $user
     */
    protected $user;

    /**
     * @ORM\Column(name="rememberMeToken", type="string", length=100, nullable=false, unique=true)
     * @var string $rememberMeToken
     */
    protected $rememberMeToken;

    /**
     * @ORM\Column(name="userAgent", type="text")
     * @var string|null $userAgent
     */
    protected $userAgent;

    /**
     * @ORM\Column(name="expireDate", type="datetime_immutable")
     * @var \DateTimeImmutable $expireDate
     */
    protected $expireDate;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getRememberMeToken(): string
    {
        return $this->rememberMeToken;
    }

    /**
     * @param string $rememberMeToken
     */
    public function setRememberMeToken(string $rememberMeToken): void
    {
        $this->rememberMeToken = $rememberMeToken;
    }

    /**
     * @return string|null
     */
    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    /**
     * @param string|null $userAgent
     */
    public function setUserAgent(?string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getExpireDate(): \DateTimeImmutable
    {
        return $this->expireDate;
    }

    /**
     * @param \DateTimeImmutable $expireDate
     */
    public function setExpireDate(\DateTimeImmutable $expireDate): void
    {
        $this->expireDate = $expireDate;
    }

    /**
     * @return array
     */
    public function getArrayCopy(): array
    {
        return [
            'uuid' => $this->getUuid()->toString(),
            'name' => $this->getUser(),
            'userHash' => $this->getRememberMeToken(),
            'userAgent' => $this->getUserAgent(),
            'expireDate' => $this->getExpireDate(),
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated()
        ];
    }
}
