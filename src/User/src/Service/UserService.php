<?php

declare(strict_types=1);

namespace Frontend\User\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Dot\Mail\Exception\MailException;
use Dot\Mail\Service\MailService;
use Frontend\App\Common\Message;
use Frontend\App\Common\UuidOrderedTimeGenerator;
use Frontend\Contact\Repository\MessageRepository;
use Frontend\User\Entity\UserRememberMe;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserAvatar;
use Frontend\User\Entity\UserDetail;
use Frontend\User\Entity\UserInterface;
use Frontend\User\Entity\UserRole;
use Frontend\User\Repository\UserRepository;
use Frontend\User\Repository\UserRoleRepository;
use Laminas\Diactoros\UploadedFile;
use Laminas\Session\Config\SessionConfig;
use Laminas\Session\SessionManager;
use Mezzio\Template\TemplateRendererInterface;

/**
 * Class UserService
 * @package Frontend\User\Service
 *
 * @Service()
 */
class UserService implements UserServiceInterface
{
    public const EXTENSIONS = [
        'image/jpg' => 'jpg',
        'image/jpeg' => 'jpg',
        'image/png' => 'png'
    ];

    /** @var EntityManager $em */
    protected $em;

    /** @var UserRepository $userRepository */
    protected $userRepository;

    /** @var UserRoleRepository $userRoleRepository */
    protected $userRoleRepository;

    /** @var UserRoleServiceInterface $userRoleService */
    protected $userRoleService;

    /** @var MailService $mailService */
    protected $mailService;

    /** @var TemplateRendererInterface $templateRenderer */
    protected $templateRenderer;

    /** @var array $config */
    protected $config;

    /** @var  SessionManager */
    protected $defaultSessionManager;

    /** @var UserRepository $repository */
    protected $repository;

    /**
     * UserService constructor.
     * @param EntityManager $em
     * @param UserRoleServiceInterface $userRoleService
     * @param MailService $mailService
     * @param TemplateRendererInterface $templateRenderer
     * @param SessionManager $defaultSessionManager
     * @param array $config
     *
     * @Inject({EntityManager::class, UserRoleServiceInterface::class, MailService::class,
     *     TemplateRendererInterface::class, SessionManager::class, "config"})
     */
    public function __construct(
        EntityManager $em,
        UserRoleServiceInterface $userRoleService,
        MailService $mailService,
        TemplateRendererInterface $templateRenderer,
        SessionManager $defaultSessionManager,
        array $config = []
    ) {
        $this->em = $em;
        $this->userRepository = $em->getRepository(User::class);
        $this->userRoleRepository = $em->getRepository(UserRole::class);
        $this->userRoleService = $userRoleService;
        $this->mailService = $mailService;
        $this->templateRenderer = $templateRenderer;
        $this->defaultSessionManager = $defaultSessionManager;
        $this->config = $config;
    }

    /**
     * @param string $uuid
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function findByUuid(string $uuid)
    {
        return $this->userRepository->findByUuid($uuid);
    }

    /**
     * @param string $identity
     * @return UserInterface
     * @throws NonUniqueResultException
     */
    public function findByIdentity(string $identity): UserInterface
    {
        return $this->userRepository->findByIdentity($identity);
    }

    /**
     * @param array $data
     * @return UserInterface
     * @throws \Exception
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createUser(array $data): UserInterface
    {
        if ($this->exists($data['email'])) {
            throw new ORMException(Message::DUPLICATE_EMAIL);
        }

        $user = new User();
        $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT))->setIdentity($data['email']);

        $detail = new UserDetail();
        $detail->setUser($user)->setFirstName($data['detail']['firstName'])->setLastName($data['detail']['lastName']);

        $user->setDetail($detail);

        if (!empty($data['status'])) {
            $user->setStatus($data['status']);
        }

        if (!empty($data['roles'])) {
            foreach ($data['roles'] as $roleName) {
                $role = $this->userRoleRepository->findByName($roleName);
                if (!$role instanceof UserRole) {
                    throw new \Exception('Role not found: ' . $roleName);
                }
                $user->addRole($role);
            }
        } else {
            $role = $this->userRoleService->findOneBy(['name' => UserRole::ROLE_USER]);
            if ($role instanceof UserRole) {
                $user->addRole($role);
            }
        }

        if (empty($user->getRoles())) {
            throw new \Exception(Message::RESTRICTION_ROLES);
        }

        $this->userRepository->saveUser($user);

        return $user;
    }


    /**
     * @param User $user
     * @param array $data
     * @return User
     * @throws ORMException
     * @throws \Doctrine\ORM\NoResultException
     * @throws NonUniqueResultException
     * @throws OptimisticLockException
     */
    public function updateUser(User $user, array $data = [])
    {
        if (isset($data['email']) && !is_null($data['email'])) {
            if ($this->exists($data['email'], $user->getUuid()->toString())) {
                throw new ORMException(Message::DUPLICATE_EMAIL);
            }
            $user->setIdentity($data['email']);
        }

        if (isset($data['password']) && !is_null($data['password'])) {
            $user->setPassword(
                password_hash($data['password'], PASSWORD_DEFAULT)
            );
        }

        if (isset($data['status']) && !empty($data['status'])) {
            $user->setStatus($data['status']);
        }

        if (isset($data['isDeleted']) && !is_null($data['isDeleted'])) {
            $user->setIsDeleted((bool)$data['isDeleted']);

            // make user anonymous
            $user->setIdentity('anonymous' . date('dmYHis') . '@dotkernel.com');
            $userDetails = $user->getDetail();
            $userDetails->setFirstName('anonymous' . date('dmYHis'));
            $userDetails->setLastName('anonymous' . date('dmYHis'));

            $user->setDetail($userDetails);
        }

        if (isset($data['hash']) && !empty($data['hash'])) {
            $user->setHash($data['hash']);
        }

        if (isset($data['detail']['firstName']) && !is_null($data['detail']['firstName'])) {
            $user->getDetail()->setFirstName($data['detail']['firstName']);
        }

        if (isset($data['detail']['lastName']) && !is_null($data['detail']['lastName'])) {
            $user->getDetail()->setLastName($data['detail']['lastName']);
        }

        if (!empty($data['avatar'])) {
            $user->setAvatar(
                $this->createAvatar($user, $data['avatar'])
            );
        }

        if (!empty($data['roles'])) {
            $user->resetRoles();
            foreach ($data['roles'] as $roleData) {
                $role = $this->userRoleService->findOneBy(['uuid' => $roleData['uuid']]);
                if ($role instanceof UserRole) {
                    $user->addRole($role);
                }
            }
        }
        if (empty($user->getRoles())) {
            throw new \Exception(Message::RESTRICTION_ROLES);
        }

        $this->userRepository->saveUser($user);

        return $user;
    }

    /**
     * @param User $user
     * @param UploadedFile $uploadedFile
     * @return UserAvatar
     */
    protected function createAvatar(User $user, UploadedFile $uploadedFile)
    {
        $path = $this->config['uploads']['user']['path'] . DIRECTORY_SEPARATOR;
        $path .= $user->getUuid()->toString() . DIRECTORY_SEPARATOR;
        if (!file_exists($path)) {
            mkdir($path, 0755);
        }

        if ($user->getAvatar() instanceof UserAvatar) {
            $avatar = $user->getAvatar();
            $this->deleteAvatarFile($path . $avatar->getName());
        } else {
            $avatar = new UserAvatar();
            $avatar->setUser($user);
        }
        $fileName = sprintf(
            'avatar-%s.%s',
            UuidOrderedTimeGenerator::generateUuid()->toString(),
            self::EXTENSIONS[$uploadedFile->getClientMediaType()]
        );
        $avatar->setName($fileName);

        $uploadedFile = new UploadedFile(
            $uploadedFile->getStream()->getMetadata()['uri'],
            $uploadedFile->getSize(),
            $uploadedFile->getError()
        );
        $uploadedFile->moveTo($path . $fileName);

        return $avatar;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function deleteAvatarFile(string $path)
    {
        if (empty($path)) {
            return false;
        }

        if (is_readable($path)) {
            return unlink($path);
        }

        return false;
    }

    /**
     * @param string $email
     * @param string|null $uuid
     * @return bool
     * @throws \Doctrine\ORM\NoResultException
     * @throws NonUniqueResultException
     */
    public function exists(string $email = '', ?string $uuid = '')
    {
        return !is_null(
            $this->userRepository->exists($email, $uuid)
        );
    }

    /**
     * @return array
     */
    public function getUsers(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * @param User $user
     * @return bool
     * @throws MailException
     */
    public function sendActivationMail(User $user): bool
    {
        if ($user->isActive()) {
            return false;
        }

        $this->mailService->setBody(
            $this->templateRenderer->render('user::activate', [
                'config' => $this->config,
                'user' => $user
            ])
        );

        $this->mailService->setSubject('Welcome');
        $this->mailService->getMessage()->addTo($user->getIdentity(), $user->getName());

        return $this->mailService->send()->isValid();
    }

    /**
     * @param array $params
     * @return User|null
     */
    public function findOneBy(array $params = []): ?User
    {
        if (empty($params)) {
            return null;
        }

        /** @var User $user */
        $user = $this->userRepository->findOneBy($params);

        return $user;
    }

    /**
     * @param User $user
     * @return User
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function activateUser(User $user): User
    {
        $this->userRepository->saveUser($user->activate());

        return $user;
    }

    /**
     * @param User $user
     * @return bool
     * @throws MailException
     */
    public function sendResetPasswordRequestedMail(User $user): bool
    {
        $this->mailService->setBody(
            $this->templateRenderer->render('user::reset-password-requested', [
                'config' => $this->config,
                'user' => $user
            ])
        );
        $this->mailService->setSubject(
            'Reset password instructions for your ' . $this->config['application']['name'] . ' account'
        );
        $this->mailService->getMessage()->addTo($user->getIdentity(), $user->getName());

        return $this->mailService->send()->isValid();
    }

    /**
     * @param string|null $hash
     * @return User|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws NonUniqueResultException
     */
    public function findByResetPasswordHash(?string $hash): ?User
    {
        if (empty($hash)) {
            return null;
        }

        return $this->userRepository->findByResetPasswordHash($hash);
    }

    /**
     * @param User $user
     * @return bool
     * @throws MailException
     */
    public function sendResetPasswordCompletedMail(User $user)
    {
        $this->mailService->setBody(
            $this->templateRenderer->render('user::reset-password-completed', [
                'config' => $this->config,
                'user' => $user
            ])
        );
        $this->mailService->setSubject(
            'You have successfully reset the password for your ' . $this->config['application']['name'] . ' account'
        );
        $this->mailService->getMessage()->addTo($user->getIdentity(), $user->getName());

        return $this->mailService->send()->isValid();
    }

    /**
     * @return UserRepository
     */
    public function getRepository(): UserRepository
    {
        return $this->userRepository;
    }

    /**
     * @param User $user
     * @param string $userAgent
     * @return void
     * @throws ORMException
     * @throws NonUniqueResultException
     * @throws OptimisticLockException
     */
    public function addRememberMeToken(User $user, string $userAgent)
    {
        $this->getRepository()->deleteExpiredCookies(new \DateTimeImmutable('now'));
        $checkUser = $this->getRepository()->findRememberMeUser($user, $userAgent);

        if (!is_null($checkUser) && $checkUser->getRememberMeToken() != $_COOKIE['rememberMe']) {
            $this->getRepository()->removeUserRememberMe($checkUser);
        }

        $rememberUser = new UserRememberMe();
        $rememberUser->setRememberMeToken(User::generateHash());
        $rememberUser->setUser($user);
        $rememberUser->setUserAgent($userAgent);
        $rememberUser->setExpireDate(new \DateTimeImmutable('@' .
            (time() + $this->config['rememberMe']['cookie']['lifetime'] )));

        $this->userRepository->saveRememberUser($rememberUser);

        $rememberToken = $rememberUser->getRememberMeToken();
        /** @var SessionConfig $config */
        $rememberConfig = $this->defaultSessionManager->getConfig();
        if ($rememberConfig->getUseCookies()) {
            setcookie(
                $this->config['rememberMe']['cookie']['name'],
                $rememberToken,
                [
                    'expires' => time() + $this->config['rememberMe']['cookie']['lifetime'],
                    'path' => $rememberConfig->getCookiePath(),
                    'domain' => $rememberConfig->getCookieDomain(),
                    'samesite' => $this->config['rememberMe']['cookie']['samesite'],
                    'secure' => $this->config['rememberMe']['cookie']['secure'],
                    'httponly' => $this->config['rememberMe']['cookie']['httponly']
                ],
            );
        }
    }

    /**
     * @return void
     * @throws ORMException
     * @throws \Doctrine\ORM\NoResultException
     * @throws NonUniqueResultException
     * @throws OptimisticLockException
     */
    public function deleteRememberMeCookie()
    {
        $cookie = $_COOKIE['rememberMe'];
        $rememberUser = $this->getRepository()->getRememberUser($cookie);
        $this->getRepository()->deleteExpiredCookies(new \DateTimeImmutable('now'));
        if (!empty($rememberUser)) {
            $this->getRepository()->removeUserRememberMe($rememberUser);
        }

        $rememberConfig = $this->defaultSessionManager->getConfig();
        setcookie(
            $this->config['rememberMe']['cookie']['name'],
            '',
            [
                'expires' => time() - 1,
                'path' => $rememberConfig->getCookiePath(),
                'domain' => $rememberConfig->getCookieDomain(),
                'samesite' => $this->config['rememberMe']['cookie']['samesite'],
                'secure' => $this->config['rememberMe']['cookie']['secure'],
                'httponly' => $this->config['rememberMe']['cookie']['httponly']
            ],
        );
    }
}
