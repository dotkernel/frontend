<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use Frontend\User\Entity\UserInterface as UserIdentityInterface;
use Mezzio\Authentication\UserInterface;

class UserIdentity implements UserInterface
{
    public function __construct(
        protected string $uuid,
        protected string $identity,
        protected array $roles = [],
        protected array $details = []
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }

    /**
     * @return iterable
     */
    public function getRoles(): iterable
    {
        return $this->roles;
    }

    /**
     * @param mixed $default
     */
    public function getDetail(string $name, $default = null): mixed
    {
        return $this->details[$name] ?? $default;
    }

    /**
     * @psalm-return array<string, mixed>
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    public static function fromEntity(UserIdentityInterface $user): self
    {
        return new self(
            $user->getUuid()->toString(),
            $user->getIdentity(),
            $user->getRoles()->map(function (UserRole $userRole) {
                return $userRole->getName();
            })->toArray(),
            $user->getDetail()->getArrayCopy(),
        );
    }
}
