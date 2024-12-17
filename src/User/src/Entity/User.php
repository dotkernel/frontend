<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Dot\Authorization\Role\RoleInterface;
use Exception;
use Frontend\App\Entity\AbstractEntity;
use Frontend\App\Entity\TimestampsTrait;
use Frontend\User\Enum\UserStatusEnum;
use Frontend\User\Repository\UserRepository;
use Ramsey\Uuid\Uuid;

use function bin2hex;
use function random_bytes;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
#[ORM\HasLifecycleCallbacks]
class User extends AbstractEntity implements UserInterface
{
    use TimestampsTrait;

    public const IS_DELETED_YES = true;
    public const IS_DELETED_NO  = false;

    public const IS_DELETED = ['1', '0'];

    #[ORM\OneToOne(targetEntity: UserDetail::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    protected UserDetail $detail;

    #[ORM\OneToOne(targetEntity: UserAvatar::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    protected ?UserAvatar $avatar;

    #[ORM\Column(name: 'identity', type: 'string', length: 191, unique: true, nullable: false)]
    protected string $identity;

    #[ORM\Column(name: 'password', type: 'string', length: 191, nullable: false)]
    protected string $password;

    #[ORM\Column(type: 'user_status_enum', options: ['default' => UserStatusEnum::Pending])]
    protected UserStatusEnum $status = UserStatusEnum::Pending;

    #[ORM\Column(name: 'hash', type: 'string', length: 64, unique: true, nullable: false)]
    protected string $hash;

    #[ORM\ManyToMany(targetEntity: UserRole::class)]
    #[ORM\JoinTable(name: 'user_roles')]
    #[ORM\JoinColumn(name: 'userUuid', referencedColumnName: 'uuid')]
    #[ORM\InverseJoinColumn(name: 'roleUuid', referencedColumnName: 'uuid')]
    protected Collection $roles;

    #[ORM\OneToMany(
        targetEntity: UserResetPassword::class,
        mappedBy: 'user',
        cascade: ['persist', 'remove'],
        fetch: 'EXTRA_LAZY'
    )]
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

    public function getStatus(): UserStatusEnum
    {
        return $this->status;
    }

    public function setStatus(UserStatusEnum $status): self
    {
        $this->status = $status;

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
            $bytes = Uuid::uuid4()->getBytes();
        }

        return bin2hex($bytes);
    }

    public function isActive(): bool
    {
        return $this->status === UserStatusEnum::Active;
    }

    public function isPending(): bool
    {
        return $this->status === UserStatusEnum::Pending;
    }

    public function isDeleted(): bool
    {
        return $this->status === UserStatusEnum::Deleted;
    }

    public function getName(): string
    {
        return $this->getDetail()->getFirstName() . ' ' . $this->getDetail()->getLastName();
    }

    public function activate(): self
    {
        return $this->setStatus(UserStatusEnum::Active);
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

    public function setResetPasswords(array $resetPasswords): self
    {
        foreach ($resetPasswords as $resetPassword) {
            $this->resetPasswords->add($resetPassword);
        }

        return $this;
    }

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
