<?php

declare(strict_types=1);

namespace Frontend\User\Repository;

use Doctrine\ORM\EntityRepository;
use Frontend\User\Entity\RememberUser;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserInterface;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;

/**
 * Class UserRepository
 * @package Frontend\User\Repository
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
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveUser(User $user)
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $email
     * @param string|null $uuid
     * @return mixed|null
     */
    public function exists(string $email = '', ?string $uuid = '')
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
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param string $email
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getUserByEmail(string $email)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('user')
            ->from(User::class, 'user')
            ->where('user.identity = :identity')->setParameter('identity', $email);
        return $qb->getQuery()->useQueryCache(true)->getOneOrNullResult();
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
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param RememberUser $rememberUser
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveRememberUser(RememberUser $rememberUser)
    {
        $em = $this->getEntityManager();
        $rememberUser->touch();

        $em->persist($rememberUser);
        $em->flush();
    }

    /**
     * @param $token
     * @return RememberUser
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getRememberUser($token): RememberUser
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('rememberUser')
            ->from(RememberUser::class, 'rememberUser')
            ->where('rememberUser.rememberMeToken = :token')
            ->setParameter('token', $token);

        return $qb->getQuery()->useQueryCache(true)->getSingleResult();
    }

    /**
     * @param User $user
     * @param string $deviceModel
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function findRememberMeUser(User $user, string $deviceModel)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('rememberUser')
            ->from(RememberUser::class, 'rememberUser')
            ->where('rememberUser.user = :uuid')
            ->setParameter('uuid', $user->getUuid(), UuidBinaryOrderedTimeType::NAME)
            ->andWhere('rememberUser.deviceModel = :deviceModel')
            ->setParameter('deviceModel', $deviceModel);

        return $qb->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }

    /**
     * @param \DateTimeImmutable $currentDate
     * @return void
     */
    public function deleteExpiredCookies(\DateTimeImmutable $currentDate)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete(RememberUser::class, 'rememberUser')
            ->where('rememberUser.expireDate <= :currentDate')
            ->setParameter('currentDate', $currentDate);

        return $qb->getQuery()->useQueryCache(true)->execute();
    }

    /**
     * @param RememberUser $rememberUser
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function removeRememberUser(RememberUser $rememberUser)
    {
        $this->getEntityManager()->remove($rememberUser);
        $this->getEntityManager()->flush();
    }
}
