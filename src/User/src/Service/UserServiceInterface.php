<?php

namespace Frontend\User\Service;

use Frontend\User\Entity\UserInterface;

/**
 * Interface UserServiceInterface
 * @package Frontend\User\Service
 */
interface UserServiceInterface
{
    /**
     * @param array $data
     * @return UserInterface
     * @throws \Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createUser(array $data): UserInterface;
}
