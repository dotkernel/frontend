<?php

declare(strict_types=1);

namespace Frontend\User\Service;

use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Dot\Mail\Service\MailService;
use Dot\Mail\Service\MailServiceInterface;
use Exception;
use Frontend\App\Common\Message;
use Frontend\App\Common\UuidOrderedTimeGenerator;
use Frontend\App\Service\CookieServiceInterface;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserAvatar;
use Frontend\User\Entity\UserDetail;
use Frontend\User\Entity\UserInterface;
use Frontend\User\Entity\UserRememberMe;
use Frontend\User\Entity\UserRole;
use Frontend\User\Repository\UserRepository;
use Frontend\User\Repository\UserRoleRepository;
use Laminas\Diactoros\UploadedFile;
use Mezzio\Template\TemplateRendererInterface;

use function file_exists;
use function is_readable;
use function mkdir;
use function password_hash;
use function sprintf;
use function time;
use function unlink;

use const PASSWORD_DEFAULT;

/**
 * @Service()
 */
class UserService implements UserServiceInterface
{
    public const EXTENSIONS = [
        'image/jpg'  => 'jpg',
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
    ];

    /**
     * @Inject({
     *     CookieServiceInterface::class,
     *     MailService::class,
     *     UserRoleServiceInterface::class,
     *     TemplateRendererInterface::class,
     *     UserRepository::class,
     *     UserRoleRepository::class,
     *     "config"
     * })
     */
    public function __construct(
        protected CookieServiceInterface $cookieService,
        protected MailService $mailService,
        protected UserRoleServiceInterface $userRoleService,
        protected TemplateRendererInterface $templateRenderer,
        protected UserRepository $userRepository,
        protected UserRoleRepository $userRoleRepository,
        protected array $config = []
    ) {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByUuid(string $uuid): ?User
    {
        return $this->userRepository->findByUuid($uuid);
    }

    /**
     * @throws Exception
     */
    public function createUser(array $data = []): UserInterface
    {
        if ($this->exists($data['email'])) {
            throw new Exception(Message::DUPLICATE_EMAIL);
        }

        $detail = (new UserDetail())
            ->setFirstName($data['detail']['firstName'] ?? null)
            ->setLastName($data['detail']['lastName'] ?? null);

        $user = (new User())
            ->setDetail($detail)
            ->setIdentity($data['identity'])
            ->setPassword(password_hash($data['password'], PASSWORD_DEFAULT))
            ->setStatus($data['status'] ?? User::STATUS_PENDING);

        $detail->setUser($user);

        if (! empty($data['roles'])) {
            foreach ($data['roles'] as $roleData) {
                $role = $this->userRoleService->findOneBy(['uuid' => $roleData['uuid']]);
                if ($role instanceof UserRole) {
                    $user->addRole($role);
                }
            }
        } else {
            $role = $this->userRoleService->findOneBy(['name' => UserRole::ROLE_USER]);
            if ($role instanceof UserRole) {
                $user->addRole($role);
            }
        }

        if ($user->getRoles()->count() < 1) {
            throw new Exception(Message::RESTRICTION_ROLES);
        }

        return $this->userRepository->saveUser($user);
    }

    /**
     * @throws Exception
     */
    public function updateUser(User $user, array $data = []): User
    {
        if (isset($data['identity'])) {
            if ($this->exists($data['identity'], $user->getUuid()->toString())) {
                throw new Exception(Message::DUPLICATE_EMAIL);
            }
        }

        if (isset($data['password'])) {
            $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
        }

        if (isset($data['status'])) {
            $user->setStatus($data['status']);
        }

        if (isset($data['isDeleted'])) {
            $user->setIsDeleted($data['isDeleted']);
        }

        if (isset($data['hash'])) {
            $user->setHash($data['hash']);
        }

        if (isset($data['detail']['firstName'])) {
            $user->getDetail()->setFirstname($data['detail']['firstName']);
        }

        if (isset($data['detail']['lastName'])) {
            $user->getDetail()->setLastName($data['detail']['lastName']);
        }

        if (! empty($data['avatar'])) {
            $user->setAvatar(
                $this->createAvatar($user, $data['avatar'])
            );
        }

        if (! empty($data['roles'])) {
            $user->resetRoles();
            foreach ($data['roles'] as $roleData) {
                $role = $this->userRoleService->findOneBy(['uuid' => $roleData['uuid']]);
                if ($role instanceof UserRole) {
                    $user->addRole($role);
                }
            }
        }

        if ($user->getRoles()->count() < 1) {
            throw new Exception(Message::RESTRICTION_ROLES);
        }

        return $this->userRepository->saveUser($user);
    }

    protected function createAvatar(User $user, UploadedFile $uploadedFile): UserAvatar
    {
        $path = sprintf('%s/%s/', $this->config['uploads']['user']['path'], $user->getUuid()->toString());
        if (! file_exists($path)) {
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

    public function exists(string $email = '', ?string $uuid = ''): bool
    {
        return ! empty(
            $this->userRepository->exists($email, $uuid)
        );
    }

    public function sendActivationMail(User $user): bool
    {
        if ($user->isActive()) {
            return false;
        }

        $this->mailService->setBody(
            $this->templateRenderer->render('user::activate', [
                'config' => $this->config,
                'user'   => $user,
            ])
        );

        $this->mailService->setSubject('Welcome');
        $this->mailService->getMessage()->addTo($user->getIdentity(), $user->getName());

        return $this->mailService->send()->isValid();
    }

    public function findOneBy(array $params = []): ?UserInterface
    {
        if (empty($params)) {
            return null;
        }

        return $this->userRepository->findOneBy($params);
    }

    public function activateUser(User $user): User
    {
        return $this->userRepository->saveUser($user->activate());
    }

    public function sendResetPasswordRequestedMail(UserInterface|User $user): bool
    {
        $this->mailService->setBody(
            $this->templateRenderer->render('user::reset-password-requested', [
                'config' => $this->config,
                'user'   => $user,
            ])
        );
        $this->mailService->setSubject(
            'Reset password instructions for your ' . $this->config['application']['name'] . ' account'
        );
        $this->mailService->getMessage()->addTo($user->getIdentity(), $user->getName());

        return $this->mailService->send()->isValid();
    }

    public function findByResetPasswordHash(?string $hash): ?User
    {
        if (empty($hash)) {
            return null;
        }

        return $this->userRepository->findByResetPasswordHash($hash);
    }

    public function sendResetPasswordCompletedMail(User $user): bool
    {
        $this->mailService->setBody(
            $this->templateRenderer->render('user::reset-password-completed', [
                'config' => $this->config,
                'user'   => $user,
            ])
        );
        $this->mailService->setSubject(
            'You have successfully reset the password for your ' . $this->config['application']['name'] . ' account'
        );
        $this->mailService->getMessage()->addTo($user->getIdentity(), $user->getName());

        return $this->mailService->send()->isValid();
    }

    public function getRepository(): UserRepository
    {
        return $this->userRepository;
    }

    /**
     * @throws Exception
     */
    public function addRememberMeToken(UserInterface|User $user, string $userAgent, array $cookies = []): void
    {
        $this->deleteExpiredRememberMeTokens();

        $token          = $cookies[$this->config['rememberMe']['cookie']['name']] ?? null;
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
     * @throws NonUniqueResultException
     */
    public function deleteRememberMeToken(array $cookies = []): void
    {
        $token = $cookies[$this->config['rememberMe']['cookie']['name']] ?? null;
        if (! empty($token)) {
            $userRememberMe = $this->getRepository()->getRememberUser($token);
            if ($userRememberMe instanceof UserRememberMe) {
                $this->getRepository()->removeUserRememberMe($userRememberMe);
            }
        }
    }

    public function deleteExpiredRememberMeTokens(): void
    {
        $this->getRepository()->deleteExpiredCookies(
            new DateTimeImmutable('now')
        );
    }
}
