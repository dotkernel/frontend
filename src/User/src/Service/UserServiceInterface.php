<?php

declare(strict_types=1);

namespace Frontend\User\Service;

use Doctrine\ORM\NonUniqueResultException;
use Dot\Mail\Exception\MailException;
use Exception;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserInterface;
use Frontend\User\Repository\UserRepository;

/**
 * Interface UserServiceInterface
 * @package Frontend\User\Service
 */
interface UserServiceInterface
{
    // TODO refactor this interface, it should only have CRUD methods.
    /**
     * @throws Exception
     */
    public function createUser(array $data): UserInterface;

    /**
     * @throws MailException
     */
    public function sendActivationMail(User $user): bool;

    public function findOneBy(array $params = []): ?User;

    public function activateUser(User $user): User;

    /**
     * @throws NonUniqueResultException
     */
    public function findByUuid(string $uuid): ?User;

    public function getRepository(): UserRepository;

    /**
     * @param UserInterface|User $user
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function addRememberMeToken(UserInterface|User $user, string $userAgent, array $cookies = []): void;

    /**
     * @throws NonUniqueResultException
     */
    public function deleteRememberMeToken(array $cookies = []): void;

    /**
     * @throws NonUniqueResultException
     */
    public function findByIdentity(string $identity): UserInterface;

    public function deleteExpiredRememberMeTokens(): void;

    /**
     * @throws Exception
     */
    public function updateUser(User $user, array $data = []): UserInterface;

    public function deleteAvatarFile(string $path): bool;

    public function exists(string $email = '', ?string $uuid = ''): bool;

    /**
     * @param UserInterface|User $user
     * @throws MailException
     */
    public function sendResetPasswordRequestedMail(UserInterface|User $user): bool;

    public function findByResetPasswordHash(?string $hash): ?User;

    /**
     * @throws MailException
     */
    public function sendResetPasswordCompletedMail(User $user): bool;
}
