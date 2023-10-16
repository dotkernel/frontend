<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

/**
 * Interface UserInterface
 */
interface UserInterface
{
    public function getUuid(): ?UuidInterface;

    public function getDetail(): ?UserDetail;

    public function setDetail(UserDetail $detail): UserInterface;

    public function getAvatar(): ?UserAvatar;

    public function setAvatar(UserAvatar $avatar): UserInterface;

    public function getIdentity(): string;

    public function setIdentity(string $identity): UserInterface;

    public function getPassword(): string;

    public function setPassword(string $password): UserInterface;

    public function getStatus(): string;

    public function setStatus(string $status): UserInterface;

    public function getRoles(): Collection;

    public function addRole(UserRole $role): UserInterface;

    public function removeRole(UserRole $role): UserInterface;

    public function getIsDeleted(): bool;

    /**
     * @return array
     */
    public function getArrayCopy(): array;

    public function activate(): UserInterface;

    public function getName(): string;
}
