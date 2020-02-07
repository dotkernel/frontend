<?php

declare(strict_types=1);

namespace Frontend\User\Service;

use Doctrine\ORM\EntityManager;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserDetail;
use Frontend\User\Entity\UserInterface;
use Frontend\User\Entity\UserRole;
use Frontend\User\Repository\UserRepository;
use Frontend\User\Repository\UserRoleRepository;

/**
 * Class UserService
 * @package Frontend\User\Service
 *
 * @Service()
 */
class UserService implements UserServiceInterface
{
    /** @var EntityManager $em */
    protected $em;

    /** @var UserRepository $userRepository */
    protected $userRepository;

    /** @var UserRoleRepository $userRoleRepository */
    protected $userRoleRepository;

    /**
     * UserService constructor.
     * @param EntityManager $em
     *
     * @Inject({EntityManager::class})
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->userRepository = $em->getRepository(User::class);
        $this->userRoleRepository = $em->getRepository(UserRole::class);

        # test stuff
//        $user = $this->createUser([
//            'identity' => 'a@a.com',
//            'password' => '123456',
//            'roles' => UserRole::ROLES,
//            'detail' => [
//                'firstname' => 'fname',
//                'lastname' => 'lname'
//            ]
//        ]);

        try {
            $user = $this->userRepository->findByIdentity('a@a.com');
            echo '<pre>';
            var_export($user->toArray());
            exit(__FILE__ . ': ' . __LINE__);
        } catch (\Exception $exception) {
            exit($exception->getMessage());
        }
        # endtest
    }

    /**
     * @param array $data
     * @return UserInterface
     * @throws \Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createUser(array $data): UserInterface
    {
        $user = (new User())->setIdentity($data['identity'])->setPassword($data['password']);
        foreach ($data['roles'] as $roleName) {
            $role = $this->userRoleRepository->findByName($roleName);
            if (!$role instanceof UserRole) {
                throw new \Exception('Role not found: ' . $roleName);
            }
            $user->addRole($role);
        }

        if (!empty($data['detail'])) {
            $userDetail = new UserDetail();
            $userDetail->setFirstname($data['detail']['firstname'])->setLastname($data['detail']['lastname']);
            $user->setDetail($userDetail);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @return array
     */
    public function getUsers(): array
    {
        return $this->userRepository->findAll();
    }
}
