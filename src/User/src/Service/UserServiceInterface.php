<?php

declare(strict_types=1);

namespace Frontend\User\Service;

use Doctrine\ORM\EntityRepository;
use Dot\Mail\Exception\MailException;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserInterface;

interface UserServiceInterface
{
    public function createUser(array $data): UserInterface;

    /**
     * @throws MailException
     */
    public function sendActivationMail(User $user): bool;

    public function findOneBy(array $params = []): ?UserInterface;

    public function activateUser(User $user): User;

    public function findByUuid(string $uuid): ?User;

    public function getRepository(): EntityRepository;

    public function addRememberMeToken(UserInterface|User $user, string $userAgent, array $cookies = []): void;

    public function deleteRememberMeToken(array $cookies = []): void;

    public function deleteExpiredRememberMeTokens(): void;

    public function updateUser(User $user, array $data = []): UserInterface;

    public function deleteAvatarFile(string $path): bool;

    public function exists(string $email = '', ?string $uuid = ''): bool;

    /**
     * @throws MailException
     */
    public function sendResetPasswordRequestedMail(UserInterface|User $user): bool;

    public function findByResetPasswordHash(?string $hash): ?User;

    /**
     * @throws MailException
     */
    public function sendResetPasswordCompletedMail(User $user): bool;
}
