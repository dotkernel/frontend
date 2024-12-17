<?php

declare(strict_types=1);

namespace Frontend\User\Repository;

use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Dot\DependencyInjection\Attribute\Entity;
use Exception;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserRememberMe;
use Frontend\User\Enum\UserStatusEnum;
use Ramsey\Uuid\Uuid;

use function is_string;
use function strlen;

/**
 * @extends EntityRepository<object>
 */
#[Entity(name: User::class)]
class UserRepository extends EntityRepository
{
    /**
     * @throws NonUniqueResultException
     */
    public function findByUuid(string $uuid): ?User
    {
        $uuid = Uuid::fromString($uuid)->getBytes();

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('user')
            ->from(User::class, 'user')
            ->where("user.uuid = :uuid")
            ->setParameter('uuid', $uuid)
            ->setMaxResults(1);

        //ignore deleted users
        $qb->andWhere('user.status != :status')->setParameter('status', UserStatusEnum::Deleted);
        return $qb->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }

    public function saveUser(User $user): User
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }

    public function exists(string $email = '', ?string $uuid = ''): ?User
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('user')
            ->from(User::class, 'user')
            ->where('user.identity = :email')->setParameter('email', $email);
        if (is_string($uuid) && strlen($uuid) > 0) {
            $uuid = Uuid::fromString($uuid)->getBytes();
            $qb->andWhere('user.uuid != :uuid')->setParameter('uuid', $uuid);
        }

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (Exception) {
            return null;
        }
    }

    public function findByResetPasswordHash(string $hash): ?User
    {
        try {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select(['user', 'resetPasswords'])->from(User::class, 'user')
                ->leftJoin('user.resetPasswords', 'resetPasswords')
                ->andWhere('resetPasswords.hash = :hash')
                ->setParameter('hash', $hash)
                ->andWhere('user.status != :deleted')
                ->setParameter('deleted', UserStatusEnum::Deleted);

            return $qb->getQuery()->useQueryCache(true)->getSingleResult();
        } catch (Exception) {
            return null;
        }
    }

    public function saveUserRememberMe(UserRememberMe $userRememberMe): void
    {
        $this->getEntityManager()->persist($userRememberMe);
        $this->getEntityManager()->flush();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getRememberUser(string $token): ?UserRememberMe
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('user_remember_me')
            ->from(UserRememberMe::class, 'user_remember_me')
            ->where('user_remember_me.rememberMeToken = :token')
            ->setParameter('token', $token)
            ->andWhere('user.status != :deleted')
            ->setParameter('deleted', UserStatusEnum::Deleted);

        return $qb->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findRememberMeUser(User $user, string $userAgent): ?UserRememberMe
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('user_remember_me')
            ->from(UserRememberMe::class, 'user_remember_me')
            ->where('user_remember_me.user = :uuid')
            ->setParameter('uuid', $user->getUuid()->getBytes())
            ->andWhere('user_remember_me.userAgent = :userAgent')
            ->setParameter('userAgent', $userAgent)
            ->andWhere('user.status != :deleted')
            ->setParameter('deleted', UserStatusEnum::Deleted);

        return $qb->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }

    public function deleteExpiredCookies(DateTimeImmutable $currentDate): mixed
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete(UserRememberMe::class, 'user_remember_me')
            ->where('user_remember_me.expireDate <= :currentDate')
            ->setParameter('currentDate', $currentDate);

        return $qb->getQuery()->useQueryCache(true)->execute();
    }

    public function removeUserRememberMe(UserRememberMe $userRememberMe): void
    {
        $this->getEntityManager()->remove($userRememberMe);
        $this->getEntityManager()->flush();
    }
}
