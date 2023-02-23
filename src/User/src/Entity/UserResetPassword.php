<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Frontend\App\Common\AbstractEntity;

/**
 * Class UserResetPassword
 * @ORM\Entity()
 * @ORM\Table(name="user_reset_password")
 * @ORM\HasLifecycleCallbacks()
 * @package Frontend\User\Entity
 */
class UserResetPassword extends AbstractEntity
{
    /**
     * @var string
     */
    final public const STATUS_COMPLETED = 'completed';
    /**
     * @var string
     */
    final public const STATUS_REQUESTED = 'requested';
    /**
     * @var string[]
     */
    final public const STATUSES = [
        self::STATUS_COMPLETED,
        self::STATUS_REQUESTED
    ];

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist", "remove"}, inversedBy="resetPasswords")
     * @ORM\JoinColumn(name="userUuid", referencedColumnName="uuid", nullable=false)
     */
    protected User $user;

    /**
     * @ORM\Column(name="expires", type="datetime_immutable", nullable=false)
     */
    protected DateTimeImmutable $expires;

    /**
     * @ORM\Column(name="hash", type="string", length=64, nullable=false, unique=true)
     */
    protected string $hash;

    /**
     * @ORM\Column(name="status", type="string", length=20, nullable=false)
     */
    protected string $status = self::STATUS_REQUESTED;

    /**
     * UserResetPassword constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->expires = DateTimeImmutable::createFromMutable(
            (new DateTime())->add(new DateInterval('P1D'))
        );
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getExpires(): DateTimeImmutable
    {
        return $this->expires;
    }

    public function setExpires(DateTimeImmutable $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param $hash
     */
    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Helper methods
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isValid(): bool
    {
        try {
            return $this->expires > (new DateTimeImmutable());
        } catch (Exception) {
        }

        return false;
    }

    public function markAsCompleted(): self
    {
        $this->status = self::STATUS_COMPLETED;

        return $this;
    }
}
