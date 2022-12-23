<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Dot\Authorization\Role\RoleInterface;
use Frontend\App\Common\AbstractEntity;
use Frontend\App\Common\UuidOrderedTimeGenerator;
use Doctrine\ORM\Mapping as ORM;
use Exception;

use function bin2hex;
use function random_bytes;

/**
 * Class User
 * @ORM\Entity(repositoryClass="Frontend\User\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks()
 * @package Frontend\User\Entity
 */
class User extends AbstractEntity implements UserInterface
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ACTIVE
    ];

    public const IS_DELETED_YES = true;
    public const IS_DELETED_NO = false;

    public const IS_DELETED = [
        self::IS_DELETED_YES,
        self::IS_DELETED_NO
    ];

    /**
     * @ORM\OneToOne(targetEntity="Frontend\User\Entity\UserDetail", cascade={"persist", "remove"}, mappedBy="user")
     */
    protected UserDetail $detail;

    /**
     * @ORM\OneToOne(targetEntity="Frontend\User\Entity\UserAvatar", cascade={"persist", "remove"}, mappedBy="user")
     */
    protected ?UserAvatar $avatar;

    /**
     * @ORM\Column(name="identity", type="string", length=191, nullable=false, unique=true)
     */
    protected string $identity;

    /**
     * @ORM\Column(name="password", type="string", length=191, nullable=false)
     */
    protected string $password;

    /**
     * @ORM\Column(name="status", type="string", length=20, columnDefinition="ENUM('pending', 'active')")
     */
    protected string $status = self::STATUS_PENDING;

    /**
     * @ORM\Column(name="isDeleted", type="boolean")
     */
    protected bool $isDeleted = self::IS_DELETED_NO;

    /**
     * @ORM\Column(name="hash", type="string", length=64, nullable=false, unique=true)
     */
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
     * User constructor.
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->roles = new ArrayCollection();
        $this->resetPasswords = new ArrayCollection();
        $this->renewHash();
    }

    /**
     * @return UserDetail|null
     */
    public function getDetail(): ?UserDetail
    {
        return $this->detail;
    }

    /**
     * @param UserDetail $detail
     * @return self
     */
    public function setDetail(UserDetail $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * @return UserAvatar|null
     */
    public function getAvatar(): ?UserAvatar
    {
        return $this->avatar;
    }

    /**
     * @param UserAvatar $avatar
     * @return self
     */
    public function setAvatar(UserAvatar $avatar): self
    {
        $this->avatar = $avatar;

        $avatar->setUser($this);

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentity(): string
    {
        return $this->identity;
    }

    /**
     * @param string $identity
     * @return self
     */
    public function setIdentity(string $identity): self
    {
        $this->identity = $identity;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return self
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     * @return self
     */
    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     * @return self
     */
    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param RoleInterface $role
     * @return self
     */
    public function addRole(RoleInterface $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    /**
     * @param RoleInterface $role
     * @return self
     */
    public function removeRole(RoleInterface $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }

        return $this;
    }

    /**
     * @return self
     */
    public function renewHash(): self
    {
        $this->hash = self::generateHash();

        return $this;
    }

    /**
     * @return string
     */
    public static function generateHash(): string
    {
        try {
            $bytes = random_bytes(32);
        } catch (Exception $exception) {
            $bytes = UuidOrderedTimeGenerator::generateUuid()->getBytes();
        }

        return bin2hex($bytes);
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * @return self
     */
    public function markAsDeleted(): self
    {
        $this->isDeleted = self::IS_DELETED_YES;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getDetail()->getFirstName() . ' ' . $this->getDetail()->getLastName();
    }

    /**
     * @return self
     */
    public function activate(): self
    {
        return $this->setStatus(self::STATUS_ACTIVE);
    }

    /**
     * @return self
     */
    public function resetRoles(): self
    {
        $this->roles->map(function (RoleInterface $role) {
            $this->removeRole($role);
        });

        $this->roles = new ArrayCollection();

        return $this;
    }

    /**
     * @return self
     */
    public function createResetPassword(): self
    {
        $resetPassword = new UserResetPassword();
        $resetPassword->setHash(self::generateHash());
        $resetPassword->setUser($this);

        $this->resetPasswords->add($resetPassword);

        return $this;
    }

    /**
     * @param UserResetPassword $resetPassword
     * @return self
     */
    public function addResetPassword(UserResetPassword $resetPassword): self
    {
        $this->resetPasswords->add($resetPassword);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getResetPasswords(): Collection
    {
        return $this->resetPasswords;
    }

    /**
     * @param UserResetPassword $resetPassword
     * @return bool
     */
    public function hasResetPassword(UserResetPassword $resetPassword): bool
    {
        return $this->resetPasswords->contains($resetPassword);
    }

    /**
     * @param UserResetPassword $resetPassword
     * @return self
     */
    public function removeResetPassword(UserResetPassword $resetPassword): self
    {
        $this->resetPasswords->removeElement($resetPassword);

        return $this;
    }

    /**
     * @param array $resetPasswords
     * @return self
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
            'uuid' => $this->getUuid()->toString(),
            'detail' => ($this->getDetail() instanceof UserDetail) ? $this->getDetail()->getArrayCopy() : null,
            'avatar' => ($this->getAvatar() instanceof UserAvatar) ? $this->getAvatar()->getArrayCopy() : null,
            'identity' => $this->getIdentity(),
            'status' => $this->getStatus(),
            'roles' => $this->getRoles()->map(function (UserRole $userRole) {
                return $userRole->getArrayCopy();
            })->toArray(),
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated()
        ];
    }
}
