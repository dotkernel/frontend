<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dot\Authorization\Role\RoleInterface;
use Frontend\App\Common\AbstractEntity;
use Frontend\User\Repository\UserRoleRepository;

#[ORM\Entity(repositoryClass: UserRoleRepository::class)]
#[ORM\Table(name: 'user_role')]
#[ORM\HasLifecycleCallbacks]
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

    #[ORM\Column(name: 'name', type: 'string', length: 30, unique: true, nullable: false)]
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
