<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use Mezzio\Authentication\UserInterface;

class UserIdentity implements UserInterface
{
    protected string $identity;
    protected array $roles;
    protected array $details;
    protected string $uuid;

    public function __construct(
        string $uuid,
        string $identity,
        array $roles = [],
        array $details = []
    ) {
        $this->uuid     = $uuid;
        $this->identity = $identity;
        $this->roles    = $roles;
        $this->details  = $details;
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
}
