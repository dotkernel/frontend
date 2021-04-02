<?php

namespace Frontend\App\Common;

/**
 * Class Identity
 * @package Frontend\App\Common
 */
class IdentityResult
{
    /** @var string $uuid */
    public string $uuid;

    /** @var string $identity */
    public string $identity;

    /** @var array $roles */
    public array $roles;

    /** @var array $details */
    public array $details;

    /**
     * IdentityResult constructor.
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
    public function getIdentity(): string
    {
        return $this->identity;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return array
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }
}
