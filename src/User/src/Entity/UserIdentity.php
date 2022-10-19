<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use Mezzio\Authentication\UserInterface;

/**
 * Class UserIdentity
 * @package Frontend\User\Entity
 */
class UserIdentity implements UserInterface
{
    /** @var string $identity */
    protected string $identity;

    /** @var array $roles */
    protected array $roles;

    /** @var array $details */
    protected array $details;

    /** @var string $uuid */
    protected string $uuid;

    /**
     * UserIdentity constructor.
     * @param string $uuid
     * @param string $identity
     * @param array $roles
     * @param array $details
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

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getIdentity(): string
    {
        return $this->identity;
    }


    /**
     * @return iterable
     * @psalm-suppress LessSpecificImplementedReturnType
     */
    public function getRoles(): iterable
    {
        return $this->roles;
    }

    /**
     * @param string $name
     * @param null|mixed $default
     * @return mixed
     */
    public function getDetail(string $name, $default = null)
    {
        return $this->details[$name] ?? $default;
    }

    /**
     * @return array
     * @psalm-return array<string, mixed>
     */
    public function getDetails(): array
    {
        return $this->details;
    }
}
