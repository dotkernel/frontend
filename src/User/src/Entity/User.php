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

/**
 * Class User
 * @ORM\Entity(repositoryClass="Frontend\User\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks()
 * @package Frontend\User\Entity
 */
class User extends AbstractEntity implements UserInterface
{
    /**
     * @var string
     */
    final public const STATUS_PENDING = 'pending';
    /**
     * @var string
     */
    final public const STATUS_ACTIVE = 'active';
    /**
     * @var string[]
     */
    final public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ACTIVE
    ];

    /**
     * @var bool
     */
    final public const IS_DELETED_YES = true;
    /**
     * @var bool
     */
    final public const IS_DELETED_NO = false;

    /**
     * @var bool[]
     */
    final public const IS_DELETED = [
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

    public function getDetail(): ?UserDetail
    {
        return $this->detail;
    }

    public function setDetail(UserDetail $userDetail): self
    {
        $this->detail = $userDetail;

        return $this;
    }

    public function getAvatar(): ?UserAvatar
    {
        return $this->avatar;
    }

    public function setAvatar(UserAvatar $userAvatar): self
    {
        $this->avatar = $userAvatar;

        $userAvatar->setUser($this);

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
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRole(RoleInterface $role): self
    {
        if (!$this->roles->contains($role)) {
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
        return $this->detail->getFirstName() . ' ' . $this->detail->getLastName();
    }

    public function activate(): self
    {
        return $this->setStatus(self::STATUS_ACTIVE);
    }

    public function resetRoles(): self
    {
        $this->roles->map(function (RoleInterface $role): void {
            $this->removeRole($role);
        });

        $this->roles = new ArrayCollection();

        return $this;
    }

    public function createResetPassword(): self
    {
        $userResetPassword = new UserResetPassword();
        $userResetPassword->setHash(self::generateHash());
        $userResetPassword->setUser($this);

        $this->resetPasswords->add($userResetPassword);

        return $this;
    }

    public function addResetPassword(UserResetPassword $userResetPassword): self
    {
        $this->resetPasswords->add($userResetPassword);

        return $this;
    }

    public function getResetPasswords(): Collection
    {
        return $this->resetPasswords;
    }

    public function hasResetPassword(UserResetPassword $userResetPassword): bool
    {
        return $this->resetPasswords->contains($userResetPassword);
    }

    public function removeResetPassword(UserResetPassword $userResetPassword): self
    {
        $this->resetPasswords->removeElement($userResetPassword);

        return $this;
    }

    public function setResetPasswords(array $resetPasswords): self
    {
        foreach ($resetPasswords as $resetPassword) {
            $this->resetPasswords->add($resetPassword);
        }

        return $this;
    }

    /**
     * @return array{uuid: string, detail: mixed[]|null, avatar: mixed[]|null, identity: string, status: string, roles: mixed[][], created: \DateTimeImmutable, updated: \DateTimeImmutable|null}
     */
    public function getArrayCopy(): array
    {
        return [
            'uuid' => $this->getUuid()->toString(),
            'detail' => $this->detail?->getArrayCopy(),
            'avatar' => $this->avatar?->getArrayCopy(),
            'identity' => $this->identity,
            'status' => $this->status,
            'roles' => $this->roles->map(static function (UserRole $userRole) : array {
                return $userRole->getArrayCopy();
            })->toArray(),
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated()
        ];
    }
}
