<?php

declare(strict_types=1);

namespace Frontend\User\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Dot\Mail\Exception\MailException;
use Dot\Mail\Service\MailService;
use Exception;
use Frontend\App\Common\Message;
use Frontend\App\Common\UuidOrderedTimeGenerator;
use Frontend\App\Service\CookieServiceInterface;
use Frontend\User\Entity\UserRememberMe;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserAvatar;
use Frontend\User\Entity\UserDetail;
use Frontend\User\Entity\UserInterface;
use Frontend\User\Entity\UserRole;
use Frontend\User\Repository\UserRepository;
use Frontend\User\Repository\UserRoleRepository;
use Laminas\Diactoros\UploadedFile;
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

    protected CookieServiceInterface $cookieService;
    protected EntityManager $em;
    protected MailService $mailService;
    protected UserRepository $userRepository;
    protected UserRoleRepository $userRoleRepository;
    protected UserRoleServiceInterface $userRoleService;
    protected TemplateRendererInterface $templateRenderer;
    protected array $config = [];

    /**
     * UserService constructor.
     * @param CookieServiceInterface $cookieService
     * @param EntityManager $em
     * @param MailService $mailService
     * @param UserRoleServiceInterface $userRoleService
     * @param TemplateRendererInterface $templateRenderer
     * @param array $config
     *
     * @Inject({
     *     CookieServiceInterface::class,
     *     EntityManager::class,
     *     MailService::class,
     *     UserRoleServiceInterface::class,
     *     TemplateRendererInterface::class,
     *     "config"
     * })
     */
    public function __construct(
        CookieServiceInterface $cookieService,
        EntityManager $em,
        MailService $mailService,
        UserRoleServiceInterface $userRoleService,
        TemplateRendererInterface $templateRenderer,
        array $config = []
    ) {
        $this->cookieService = $cookieService;
        $this->mailService = $mailService;
        $this->userRepository = $em->getRepository(User::class);
        $this->userRoleRepository = $em->getRepository(UserRole::class);
        $this->userRoleService = $userRoleService;
        $this->templateRenderer = $templateRenderer;
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
     * @throws Exception
     * @throws ORMException
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
                    throw new Exception('Role not found: ' . $roleName);
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
            throw new Exception(Message::RESTRICTION_ROLES);
        }

        $this->userRepository->saveUser($user);

        return $user;
    }


    /**
     * @param User $user
     * @param array $data
     * @return UserInterface
     * @throws ORMException
     */
    public function updateUser(User $user, array $data = []): UserInterface
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
            throw new Exception(Message::RESTRICTION_ROLES);
        }

        $this->userRepository->saveUser($user);

        return $user;
    }

    /**
     * @param User $user
     * @param UploadedFile $uploadedFile
     * @return UserAvatar
     */
    protected function createAvatar(User $user, UploadedFile $uploadedFile): UserAvatar
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
    public function deleteAvatarFile(string $path): bool
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
     */
    public function exists(string $email = '', ?string $uuid = ''): bool
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
     * @param array $cookies
     * @return void
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function addRememberMeToken(User $user, string $userAgent, array $cookies = []): void
    {
        $this->deleteExpiredRememberMeTokens();

        $token = $cookies[$this->config['rememberMe']['cookie']['name']] ?? null;
        $userRememberMe = $this->getRepository()->findRememberMeUser($user, $userAgent);
        if ($userRememberMe instanceof UserRememberMe && $userRememberMe->getRememberMeToken() !== $token) {
            $this->getRepository()->removeUserRememberMe($userRememberMe);
        }

        $expires = time() +
            ($this->config['rememberMe']['cookie']['lifetime'] ?? $this->config['session_config']['cookie_lifetime']);

        $userRememberMe = (new UserRememberMe())
            ->setRememberMeToken(User::generateHash())
            ->setUser($user)
            ->setUserAgent($userAgent)
            ->setExpireDate(
                new DateTimeImmutable('@' . $expires)
            );
        $this->userRepository->saveUserRememberMe($userRememberMe);

        $this->cookieService->setCookie(
            $this->config['rememberMe']['cookie']['name'],
            $userRememberMe->getRememberMeToken(),
            [
                'expires' => $expires,
            ]
        );
    }

    /**
     * @param array $cookies
     * @return void
     * @throws NonUniqueResultException
     */
    public function deleteRememberMeToken(array $cookies = []): void
    {
        $token = $cookies[$this->config['rememberMe']['cookie']['name']] ?? null;
        if (!empty($token)) {
            $userRememberMe = $this->getRepository()->getRememberUser($token);
            if ($userRememberMe instanceof UserRememberMe) {
                $this->getRepository()->removeUserRememberMe($userRememberMe);
            }
        }
    }

    /**
     * @return void
     */
    public function deleteExpiredRememberMeTokens(): void
    {
        $this->getRepository()->deleteExpiredCookies(
            new DateTimeImmutable('now')
        );
    }
}
