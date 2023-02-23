<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use Mezzio\Authentication\UserInterface;

/**
 * Class UserIdentity
 * @package Frontend\User\Entity
 */
final class UserIdentity implements UserInterface
{
    private readonly string $identity;
    private readonly array $roles;
    private readonly array $details;
    private readonly string $uuid;

    /**
     * UserIdentity constructor.
     */
    public function __construct(
        string $uuid,
        string $identity,
        array $roles = [],
        array $details = []
    ) {
        $this->uuid = $uuid;
        $this->identity = $identity;
        $this->roles = $roles;
        $this->details = $details;
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
     * @psalm-suppress LessSpecificImplementedReturnType
     */
    public function getRoles(): iterable
    {
        return $this->roles;
    }

    /**
     * @param null|mixed $default
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
