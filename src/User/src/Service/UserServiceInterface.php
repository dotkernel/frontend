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
     * @param array $data
     * @return UserInterface
     * @throws Exception
     */
    public function createUser(array $data): UserInterface;

    /**
     * @param User $user
     * @return bool
     * @throws MailException
     */
    public function sendActivationMail(User $user): bool;

    /**
     * @param array $params
     * @return User|null
     */
    public function findOneBy(array $params = []): ?User;

    /**
     * @param User $user
     * @return User
     */
    public function activateUser(User $user): User;

    /**
     * @param string $uuid
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function findByUuid(string $uuid): ?User;

    /**
     * @return UserRepository
     */
    public function getRepository(): UserRepository;

    /**
     * @param UserInterface|User $user
     * @param string $userAgent
     * @param array $cookies
     * @return void
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function addRememberMeToken(UserInterface|User $user, string $userAgent, array $cookies = []): void;

    /**
     * @param array $cookies
     * @return void
     * @throws NonUniqueResultException
     */
    public function deleteRememberMeToken(array $cookies = []): void;

    /**
     * @param string $identity
     * @return UserInterface
     * @throws NonUniqueResultException
     */
    public function findByIdentity(string $identity): UserInterface;

    /**
     * @return void
     */
    public function deleteExpiredRememberMeTokens(): void;

    /**
     * @param User $user
     * @param array $data
     * @return UserInterface
     * @throws Exception
     */
    public function updateUser(User $user, array $data = []): UserInterface;

    /**
     * @param string $path
     * @return bool
     */
    public function deleteAvatarFile(string $path): bool;

    /**
     * @param string $email
     * @param string|null $uuid
     * @return bool
     */
    public function exists(string $email = '', ?string $uuid = ''): bool;

    /**
     * @param UserInterface|User $user
     * @return bool
     * @throws MailException
     */
    public function sendResetPasswordRequestedMail(UserInterface|User $user): bool;

    /**
     * @param string|null $hash
     * @return User|null
     */
    public function findByResetPasswordHash(?string $hash): ?User;

    /**
     * @param User $user
     * @return bool
     * @throws MailException
     */
    public function sendResetPasswordCompletedMail(User $user): bool;
}
