<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dot\Authorization\Role\RoleInterface;
use Frontend\App\Common\AbstractEntity;

/**
 * @ORM\Entity(repositoryClass="Frontend\User\Repository\UserRoleRepository")
 * @ORM\Table(name="user_role")
 * @ORM\HasLifecycleCallbacks()
 */
class UserRole extends AbstractEntity implements RoleInterface
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER  = 'user';
    public const ROLE_GUEST = 'guest';
    public const ROLES      = [
        self::ROLE_ADMIN,
        self::ROLE_USER,
        self::ROLE_GUEST,
    ];

    /** @ORM\Column(name="name", type="string", length=30, nullable=false, unique=true) */
    protected string $name;

    public function __construct()
    {
        parent::__construct();
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return UserRole
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function getArrayCopy(): array
    {
        return [
            'uuid'    => $this->getUuid()->toString(),
            'name'    => $this->getName(),
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated(),
        ];
    }
}
