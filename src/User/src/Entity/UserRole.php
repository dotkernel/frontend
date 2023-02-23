<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dot\Authorization\Role\RoleInterface;
use Frontend\App\Common\AbstractEntity;

/**
 * Class UserRole
 * @ORM\Entity(repositoryClass="Frontend\User\Repository\UserRoleRepository")
 * @ORM\Table(name="user_role")
 * @ORM\HasLifecycleCallbacks()
 * @package Frontend\User\Entity
 */
class UserRole extends AbstractEntity implements RoleInterface
{
    /**
     * @var string
     */
    final public const ROLE_ADMIN = 'admin';
    /**
     * @var string
     */
    final public const ROLE_USER = 'user';
    /**
     * @var string
     */
    final public const ROLE_GUEST = 'guest';
    /**
     * @var string[]
     */
    final public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_USER,
        self::ROLE_GUEST
    ];

    /**
     * @ORM\Column(name="name", type="string", length=30, nullable=false, unique=true)
     */
    protected string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array{uuid: string, name: string, created: \DateTimeImmutable, updated: \DateTimeImmutable|null}
     */
    public function getArrayCopy(): array
    {
        return [
            'uuid' => $this->getUuid()->toString(),
            'name' => $this->name,
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated()
        ];
    }
}
