<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Frontend\App\Common\AbstractEntity;

/**
 * Class RememberUser
 * @ORM\Entity()
 * @ORM\Table(name="remember_user")
 * @ORM\HasLifecycleCallbacks()
 * @package Frontend\User\Entity
 */
class RememberUser extends AbstractEntity
{
    /**
     * @ORM\OneToOne(targetEntity="Frontend\User\Entity\User")
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
     * @ORM\Column(name="deviceModel", type="string", length=100)
     * @var string|null $deviceModel
     */
    protected $deviceModel;

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
    public function getDeviceModel(): ?string
    {
        return $this->deviceModel;
    }

    /**
     * @param string|null $deviceModel
     */
    public function setDeviceModel(?string $deviceModel): void
    {
        $this->deviceModel = $deviceModel;
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
            'name' => $this->getuser(),
            'userHash' => $this->getRememberMeToken(),
            'deviceModel' => $this->getDeviceModel(),
            'expireDate' => $this->getExpireDate(),
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated()
        ];
    }
}
