<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Dot\Authorization\Role\RoleInterface;
use Exception;
use Frontend\App\Common\AbstractEntity;
use Frontend\App\Common\UuidOrderedTimeGenerator;

use function bin2hex;
use function random_bytes;

/**
 * @ORM\Entity(repositoryClass="Frontend\User\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends AbstractEntity implements UserInterface
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE  = 'active';
    public const STATUSES       = [
        self::STATUS_PENDING,
        self::STATUS_ACTIVE,
    ];

    public const IS_DELETED_YES = true;
    public const IS_DELETED_NO  = false;

    public const IS_DELETED = [
        self::IS_DELETED_YES,
        self::IS_DELETED_NO,
    ];

    /** @ORM\OneToOne(targetEntity="Frontend\User\Entity\UserDetail", cascade={"persist", "remove"}, mappedBy="user") */
    protected UserDetail $detail;

    /** @ORM\OneToOne(targetEntity="Frontend\User\Entity\UserAvatar", cascade={"persist", "remove"}, mappedBy="user") */
    protected ?UserAvatar $avatar;

    /** @ORM\Column(name="identity", type="string", length=191, nullable=false, unique=true) */
    protected string $identity;

    /** @ORM\Column(name="password", type="string", length=191, nullable=false) */
    protected string $password;

    /** @ORM\Column(name="status", type="string", length=20, columnDefinition="ENUM('pending', 'active')") */
    protected string $status = self::STATUS_PENDING;

    /** @ORM\Column(name="isDeleted", type="boolean") */
    protected bool $isDeleted = self::IS_DELETED_NO;

    /** @ORM\Column(name="hash", type="string", length=64, nullable=false, unique=true) */
    protected string $hash;

    /**
     * @ORM\ManyToMany(targetEntity="Frontend\User\Entity\UserRole")
     * @ORM\JoinTable(
     *     name="user_roles",
     *     joinColumns={@ORM\JoinColumn(name="userUuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="roleUuid", referencedColumnName="uuid")}
     * )
     */
    protected Collection $roles;

    /**
     * @ORM\OneToMany(targetEntity="UserResetPassword",
     *     cascade={"persist", "remove"}, mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected Collection $resetPasswords;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->roles          = new ArrayCollection();
        $this->resetPasswords = new ArrayCollection();
        $this->renewHash();
    }

    public function getDetail(): ?UserDetail
    {
        return $this->detail;
    }

    public function setDetail(UserDetail $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    public function getAvatar(): ?UserAvatar
    {
        return $this->avatar;
    }

    public function setAvatar(UserAvatar $avatar): self
    {
        $this->avatar = $avatar;

        $avatar->setUser($this);

        return $this;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function setIdentity(string $identity): self
    {
        $this->identity = $identity;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getIsDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(RoleInterface $role): self
    {
        if (! $this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRole(RoleInterface $role): self
    {
        if (! $this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }

        return $this;
    }

    public function renewHash(): self
    {
        $this->hash = self::generateHash();

        return $this;
    }

    public static function generateHash(): string
    {
        try {
            $bytes = random_bytes(32);
        } catch (Exception) {
            $bytes = UuidOrderedTimeGenerator::generateUuid()->getBytes();
        }

        return bin2hex($bytes);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function markAsDeleted(): self
    {
        $this->isDeleted = self::IS_DELETED_YES;

        return $this;
    }

    public function getName(): string
    {
        return $this->getDetail()->getFirstName() . ' ' . $this->getDetail()->getLastName();
    }

    public function activate(): self
    {
        return $this->setStatus(self::STATUS_ACTIVE);
    }

    public function resetRoles(): self
    {
        $this->roles->map(function (RoleInterface $role) {
            $this->removeRole($role);
        });

        $this->roles = new ArrayCollection();

        return $this;
    }

    public function createResetPassword(): self
    {
        $resetPassword = new UserResetPassword();
        $resetPassword->setHash(self::generateHash());
        $resetPassword->setUser($this);

        $this->resetPasswords->add($resetPassword);

        return $this;
    }

    public function addResetPassword(UserResetPassword $resetPassword): self
    {
        $this->resetPasswords->add($resetPassword);

        return $this;
    }

    public function getResetPasswords(): Collection
    {
        return $this->resetPasswords;
    }

    public function hasResetPassword(UserResetPassword $resetPassword): bool
    {
        return $this->resetPasswords->contains($resetPassword);
    }

    public function removeResetPassword(UserResetPassword $resetPassword): self
    {
        $this->resetPasswords->removeElement($resetPassword);

        return $this;
    }

    /**
     * @param array $resetPasswords
     */
    public function setResetPasswords(array $resetPasswords): self
    {
        foreach ($resetPasswords as $resetPassword) {
            $this->resetPasswords->add($resetPassword);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getArrayCopy(): array
    {
        return [
            'uuid'     => $this->getUuid()->toString(),
            'detail'   => $this->getDetail()?->getArrayCopy(),
            'avatar'   => $this->getAvatar()?->getArrayCopy(),
            'identity' => $this->getIdentity(),
            'status'   => $this->getStatus(),
            'roles'    => $this->getRoles()->map(function (UserRole $userRole) {
                return $userRole->getArrayCopy();
            })->toArray(),
            'created'  => $this->getCreated(),
            'updated'  => $this->getUpdated(),
        ];
    }
}
