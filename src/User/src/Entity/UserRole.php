<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Frontend\App\Entity\AbstractEntity;

/**
 * Class UserRole
 * @ORM\Entity(repositoryClass="Frontend\User\Repository\UserRoleRepository")
 * @ORM\Table(name="user_role")
 * @ORM\HasLifecycleCallbacks()
 * @package Frontend\User\Entity
 */
class UserRole extends AbstractEntity
{
    const ROLE_ADMIN = 'admin';
    const ROLE_MEMBER = 'member';
    const ROLES = [
        self::ROLE_MEMBER,
        self::ROLE_ADMIN
    ];

    /**
     * @ORM\Column(name="name", type="string", length=30, nullable=false, unique=true)
     * @var string $name
     */
    protected $name;

    /**
     * UserRole constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
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
    public function toArray(): array
    {
        return [
            'uuid' => $this->getUuid()->toString(),
            'name' => $this->getName(),
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated()
        ];
    }
}
