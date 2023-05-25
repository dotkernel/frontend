<?php

declare(strict_types=1);

namespace Frontend\User\Repository;

use Doctrine\ORM\EntityRepository;
use Frontend\User\Entity\UserRememberMe;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserInterface;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Doctrine\ORM\NonUniqueResultException;
use DateTimeImmutable;
use Exception;

/**
 * Class UserRepository
 * @package Frontend\User\Repository
 * @extends EntityRepository<object>
 */
class UserRepository extends EntityRepository
{
    /**
     * @param string $uuid
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function findByUuid(string $uuid): ?User
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('user')
            ->from(User::class, 'user')
            ->where("user.uuid = :uuid")
            ->setParameter('uuid', $uuid, UuidBinaryOrderedTimeType::NAME)
            ->setMaxResults(1);
        return $qb->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }

    /**
     * @param string $identity
     * @return UserInterface
     * @throws NonUniqueResultException
     */
    public function findByIdentity(string $identity): UserInterface
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('user')
            ->from(User::class, 'user')
            ->andWhere("user.identity = :identity")
            ->setParameter('identity', $identity)
            ->setMaxResults(1);
        return $qb->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }

    /**
     * @param User $user
     * @return User
     */
    public function saveUser(User $user): User
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }

    /**
     * @param string $email
     * @param string|null $uuid
     * @return User|null
     */
    public function exists(string $email = '', ?string $uuid = ''): ?User
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('user')
            ->from(User::class, 'user')
            ->where('user.identity = :email')->setParameter('email', $email)
            ->andWhere('user.isDeleted = :isDeleted')->setParameter('isDeleted', User::IS_DELETED_NO);
        if (!empty($uuid)) {
            $qb->andWhere('user.uuid != :uuid')->setParameter('uuid', $uuid, UuidBinaryOrderedTimeType::NAME);
        }

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @param string $hash
     * @return User|null
     */
    public function findByResetPasswordHash(string $hash): ?User
    {
        try {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select(['user', 'resetPasswords'])->from(User::class, 'user')
                ->leftJoin('user.resetPasswords', 'resetPasswords')
                ->andWhere('resetPasswords.hash = :hash')->setParameter('hash', $hash);

            return $qb->getQuery()->useQueryCache(true)->getSingleResult();
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @param UserRememberMe $userRememberMe
     * @return void
     */
    public function saveUserRememberMe(UserRememberMe $userRememberMe): void
    {
        $this->getEntityManager()->persist($userRememberMe);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $token
     * @return UserRememberMe|null
     * @throws NonUniqueResultException
     */
    public function getRememberUser($token): ?UserRememberMe
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('user_remember_me')
            ->from(UserRememberMe::class, 'user_remember_me')
            ->where('user_remember_me.rememberMeToken = :token')
            ->setParameter('token', $token);

        return $qb->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }

    /**
     * @param User $user
     * @param string $userAgent
     * @return UserRememberMe|null
     * @throws NonUniqueResultException
     */
    public function findRememberMeUser(User $user, string $userAgent): ?UserRememberMe
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('user_remember_me')
            ->from(UserRememberMe::class, 'user_remember_me')
            ->where('user_remember_me.user = :uuid')
            ->setParameter('uuid', $user->getUuid(), UuidBinaryOrderedTimeType::NAME)
            ->andWhere('user_remember_me.userAgent = :userAgent')
            ->setParameter('userAgent', $userAgent);


        return $qb->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }

    /**
     * @param DateTimeImmutable $currentDate
     * @return mixed
     */
    public function deleteExpiredCookies(DateTimeImmutable $currentDate): mixed
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete(UserRememberMe::class, 'user_remember_me')
            ->where('user_remember_me.expireDate <= :currentDate')
            ->setParameter('currentDate', $currentDate);

        return $qb->getQuery()->useQueryCache(true)->execute();
    }

    /**
     * @param UserRememberMe $userRememberMe
     * @return void
     */
    public function removeUserRememberMe(UserRememberMe $userRememberMe): void
    {
        $this->getEntityManager()->remove($userRememberMe);
        $this->getEntityManager()->flush();
    }
}
