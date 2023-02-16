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
     */
    public function findByUuid(string $uuid): ?User;

    /**
     * @return UserRepository
     */
    public function getRepository(): UserRepository;

    /**
     * @param User $user
     * @param string $userAgent
     * @param array $cookies
     * @return void
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function addRememberMeToken(User $user, string $userAgent, array $cookies = []): void;

    /**
     * @param array $cookies
     * @return void
     * @throws NonUniqueResultException
     */
    public function deleteRememberMeToken(array $cookies = []): void;
}
