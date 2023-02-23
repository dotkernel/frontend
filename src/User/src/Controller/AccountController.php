<?php

declare(strict_types=1);

namespace Frontend\User\Controller;

use Doctrine\ORM\NonUniqueResultException;
use Dot\Controller\AbstractActionController;
use Dot\DebugBar\DebugBar;
use Dot\FlashMessenger\FlashMessengerInterface;
use Fig\Http\Message\RequestMethodInterface;
use Frontend\App\Common\Message;
use Frontend\Plugin\FormsPlugin;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserIdentity;
use Frontend\User\Entity\UserResetPassword;
use Frontend\User\Form\ProfileDeleteForm;
use Frontend\User\Form\ProfileDetailsForm;
use Frontend\User\Form\ProfilePasswordForm;
use Frontend\User\Form\RequestResetPasswordForm;
use Frontend\User\Form\ResetPasswordForm;
use Frontend\User\Form\UploadAvatarForm;
use Frontend\User\Service\UserServiceInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Dot\AnnotatedServices\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;
use Exception;

/**
 * Class AccountController
 * @package Frontend\User\Controller
 */
final class AccountController extends AbstractActionController
{
    private readonly RouterInterface $router;
    private readonly TemplateRendererInterface $templateRenderer;
    private readonly UserServiceInterface $userService;
    private readonly AuthenticationServiceInterface $authenticationService;
    private readonly FlashMessengerInterface $flashMessenger;
    private readonly FormsPlugin $formsPlugin;
    private readonly DebugBar $debugBar;

    /**
     * AccountController constructor.
     *
     * @Inject({
     *     UserServiceInterface::class,
     *     RouterInterface::class,
     *     TemplateRendererInterface::class,
     *     AuthenticationService::class,
     *     FlashMessengerInterface::class,
     *     FormsPlugin::class,
     *     DebugBar::class
     * })
     */
    public function __construct(
        UserServiceInterface $userService,
        RouterInterface $router,
        TemplateRendererInterface $templateRenderer,
        AuthenticationService $authenticationService,
        FlashMessengerInterface $flashMessenger,
        FormsPlugin $formsPlugin,
        DebugBar $debugBar
    ) {
        $this->userService = $userService;
        $this->router = $router;
        $this->templateRenderer = $templateRenderer;
        $this->authenticationService = $authenticationService;
        $this->flashMessenger = $flashMessenger;
        $this->formsPlugin = $formsPlugin;
        $this->debugBar = $debugBar;
    }

    public function activateAction(): ResponseInterface
    {
        $hash = $this->getRequest()->getAttribute('hash', false);
        if (!$hash) {
            $this->flashMessenger->addError(sprintf(Message::MISSING_PARAMETER, 'hash'), 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        $user = $this->userService->findOneBy(['hash' => $hash]);
        if (!($user instanceof User)) {
            $this->flashMessenger->addError(Message::INVALID_ACTIVATION_CODE, 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        if ($user->getStatus() === User::STATUS_ACTIVE) {
            $this->flashMessenger->addError(Message::USER_ALREADY_ACTIVATED, 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        try {
            $this->userService->activateUser($user);
            $this->debugBar->stackData();
        } catch (Exception $exception) {
            $this->flashMessenger->addError($exception->getMessage(), 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        $this->flashMessenger->addSuccess(Message::USER_ACTIVATED_SUCCESSFULLY, 'user-login');
        return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
    }

    public function unregisterAction(): ResponseInterface
    {
        $hash = $this->getRequest()->getAttribute('hash', false);
        if (! $hash) {
            $this->flashMessenger->addError(sprintf(Message::MISSING_PARAMETER, 'hash'), 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        $user = $this->userService->findOneBy(['hash' => $hash]);
        if (!($user instanceof User)) {
            $this->flashMessenger->addError(Message::INVALID_ACTIVATION_CODE, 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        if ($user->getIsDeleted() === User::IS_DELETED_YES) {
            $this->flashMessenger->addError(Message::USER_ALREADY_DEACTIVATED, 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        if ($user->getStatus() != User::STATUS_PENDING) {
            $this->flashMessenger->addError(Message::USER_UNREGISTER_STATUS, 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        try {
            $this->userService->updateUser($user, ['isDeleted' => User::IS_DELETED_YES]);
            $this->debugBar->stackData();
        } catch (Exception $exception) {
            $this->flashMessenger->addError($exception->getMessage(), 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        $this->flashMessenger->addSuccess(Message::USER_DEACTIVATED_SUCCESSFULLY, 'user-login');
        return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
    }

    public function requestResetPasswordAction(): ResponseInterface
    {
        $requestResetPasswordForm = new RequestResetPasswordForm();

        if (RequestMethodInterface::METHOD_POST === $this->getRequest()->getMethod()) {
            $requestResetPasswordForm->setData($this->getRequest()->getParsedBody());
            if (!$requestResetPasswordForm->isValid()) {
                $this->flashMessenger->addError($this->formsPlugin->getMessages($requestResetPasswordForm), 'request-reset');
                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            $user = $this->userService->findOneBy(['identity' => $requestResetPasswordForm->getData()['identity']]);
            if (!($user instanceof User)) {
                $this->flashMessenger->addInfo(Message::MAIL_SENT_RESET_PASSWORD, 'request-reset');
                return new RedirectResponse($this->getRequest()->getUri());
            }

            try {
                $user = $this->userService->updateUser($user->createResetPassword());
                $this->debugBar->stackData();
            } catch (Exception $exception) {
                $this->flashMessenger->addError($exception->getMessage(), 'request-reset');
                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            try {
                $this->userService->sendResetPasswordRequestedMail($user);
            } catch (Exception $exception) {
                $this->flashMessenger->addError($exception->getMessage(), 'request-reset');
                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            $this->flashMessenger->addInfo(Message::MAIL_SENT_RESET_PASSWORD, 'request-reset');
            return new RedirectResponse($this->getRequest()->getUri());
        }

        return new HtmlResponse(
            $this->templateRenderer->render('user::request-reset-form', [
                'form' => $requestResetPasswordForm
            ])
        );
    }

    public function resetPasswordAction(): ResponseInterface
    {
        $resetPasswordForm = new ResetPasswordForm();
        $hash = $this->getRequest()->getAttribute('hash') ?? null;
        if ($this->getRequest()->getMethod() === RequestMethodInterface::METHOD_POST) {
            $user = $this->userService->findByResetPasswordHash($hash);
            if (!($user instanceof User)) {
                $this->flashMessenger->addError(
                    sprintf(Message::RESET_PASSWORD_NOT_FOUND, $hash),
                    'reset-password'
                );

                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            /** @var UserResetPassword $resetPasswordRequest */
            $resetPasswordRequest = $user->getResetPasswords()->current();
            if (!$resetPasswordRequest->isValid()) {
                $this->flashMessenger->addError(sprintf(Message::RESET_PASSWORD_EXPIRED, $hash), 'reset-password');

                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            if ($resetPasswordRequest->isCompleted()) {
                $this->flashMessenger->addError(sprintf(Message::RESET_PASSWORD_USED, $hash), 'reset-password');

                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            $resetPasswordForm->setData($this->getRequest()->getParsedBody());
            if (!$resetPasswordForm->isValid()) {
                $this->flashMessenger->addError($this->formsPlugin->getMessages($resetPasswordForm), 'reset-password');

                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            try {
                $this->userService->updateUser(
                    $resetPasswordRequest->markAsCompleted()->getUser(),
                    $resetPasswordForm->getData()
                );
                $this->debugBar->stackData();
            } catch (Exception $exception) {
                $this->flashMessenger->addError($exception->getMessage(), 'reset-password');

                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            try {
                $this->userService->sendResetPasswordCompletedMail($user);
            } catch (Exception $exception) {
                $this->flashMessenger->addError($exception->getMessage(), 'reset-password');

                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            $this->flashMessenger->addSuccess(Message::PASSWORD_RESET_SUCCESSFULLY, 'user-login');

            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        return new HtmlResponse(
            $this->templateRenderer->render('user::reset-password-form', [
                'form' => $resetPasswordForm
            ])
        );
    }

    /**
     * @throws NonUniqueResultException
     */
    public function avatarAction(): ResponseInterface
    {
        /** @var UserIdentity $identity */
        $identity = $this->authenticationService->getIdentity();

        $user = $this->userService->findByUuid($identity->getUuid());
        $uploadAvatarForm = new UploadAvatarForm();
        if (RequestMethodInterface::METHOD_POST === $this->request->getMethod()) {
            $file = $this->request->getUploadedFiles()['avatar']['image'] ?? '';
            if ($file->getSize() === 0) {
                $this->flashMessenger->addWarning('Please select a file for upload.', 'profile-avatar');
                return new RedirectResponse($this->router->generateUri(
                    "account",
                    ['action' => 'avatar']
                ));
            }

            try {
                $this->userService->updateUser($user, ['avatar' => $file]);
                $this->debugBar->stackData();
            } catch (Exception) {
                $this->flashMessenger->addError('Something went wrong updating your profile image!', 'profile-avatar');
                return new RedirectResponse($this->router->generateUri(
                    "account",
                    ['action' => 'avatar']
                ));
            }

            $this->flashMessenger->addSuccess('Profile image updated successfully!', 'profile-avatar');
            return new RedirectResponse($this->router->generateUri(
                "account",
                ['action' => 'avatar']
            ));
        }

        return new HtmlResponse(
            $this->templateRenderer->render('user::profile', [
                'action' => 'avatar',
                'content' => $this->templateRenderer->render('profile::avatar', [
                    'user' => $user,
                    'form' => $uploadAvatarForm
                ]),
            ])
        );
    }

    /**
     * @throws NonUniqueResultException
     */
    public function detailsAction(): ResponseInterface
    {
        /** @var UserIdentity $identity */
        $identity = $this->authenticationService->getIdentity();

        $user = $this->userService->findByUuid($identity->getUuid());
        $profileDetailsForm = new ProfileDetailsForm();

        $shouldRebind = $this->flashMessenger->getData('shouldRebind') ?? true;
        if ($shouldRebind) {
            $this->formsPlugin->restoreState($profileDetailsForm);
        }

        if (!is_null($user)) {
            $userDetails['detail']['firstName'] = $user->getDetail()->getFirstName();
            $userDetails['detail']['lastName'] = $user->getDetail()->getLastName();
            $profileDetailsForm->setData($userDetails);
        }

        return new HtmlResponse(
            $this->templateRenderer->render('user::profile', [
                'action' => 'details',
                'content' => $this->templateRenderer->render('profile::details', [
                    'form' => $profileDetailsForm
                ]),
            ])
        );
    }

    /**
     * @throws NonUniqueResultException
     */
    public function changePasswordAction(): ResponseInterface
    {
        /** @var UserIdentity $identity */
        $identity = $this->authenticationService->getIdentity();

        $user = $this->userService->findByUuid($identity->getUuid());

        $profilePasswordForm = new ProfilePasswordForm();

        $shouldRebind = $this->flashMessenger->getData('shouldRebind') ?? true;
        if ($shouldRebind) {
            $this->formsPlugin->restoreState($profilePasswordForm);
        }

        if (RequestMethodInterface::METHOD_POST === $this->request->getMethod()) {
            $profilePasswordForm->setData($this->request->getParsedBody());
            if ($profilePasswordForm->isValid()) {
                $userData = $profilePasswordForm->getData();
                try {
                    $this->userService->updateUser($user, $userData);
                    $this->debugBar->stackData();
                } catch (Exception $exception) {
                    $this->flashMessenger->addData('shouldRebind', true);
                    $this->formsPlugin->saveState($profilePasswordForm);
                    $this->flashMessenger->addError($exception->getMessage(), 'profile-password');

                    return new RedirectResponse($this->request->getUri(), 303);
                }

                // logout and enter new password to login
                $this->authenticationService->clearIdentity();

                $this->flashMessenger->addSuccess('Password updated. Login with your new credentials.', 'user-login');
                return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
            }
            $this->flashMessenger->addData('shouldRebind', true);
            $this->formsPlugin->saveState($profilePasswordForm);
            $this->flashMessenger->addError($this->formsPlugin->getMessages($profilePasswordForm), 'profile-password');
            return new RedirectResponse($this->request->getUri(), 303);
        }

        return new HtmlResponse(
            $this->templateRenderer->render('user::profile', [
                'action' => 'change-password',
                'content' => $this->templateRenderer->render('profile::change-password', [
                    'form' => $profilePasswordForm
                ]),
            ])
        );
    }

    /**
     * @throws NonUniqueResultException
     */
    public function deleteAccountAction(): ResponseInterface
    {
        /** @var UserIdentity $identity */
        $identity = $this->authenticationService->getIdentity();

        $user = $this->userService->findByUuid($identity->getUuid());

        $profileDeleteForm = new ProfileDeleteForm();

        $shouldRebind = $this->flashMessenger->getData('shouldRebind') ?? true;
        if ($shouldRebind) {
            $this->formsPlugin->restoreState($profileDeleteForm);
        }

        if (RequestMethodInterface::METHOD_POST === $this->request->getMethod()) {
            $profileDeleteForm->setData($this->request->getParsedBody());
            if ($profileDeleteForm->isValid()) {
                $userData = $profileDeleteForm->getData();
                try {
                    $this->userService->updateUser($user, $userData);
                    $this->debugBar->stackData();
                } catch (Exception $exception) {
                    $this->flashMessenger->addData('shouldRebind', true);
                    $this->formsPlugin->saveState($profileDeleteForm);
                    $this->flashMessenger->addError($exception->getMessage(), 'profile-delete');

                    return new RedirectResponse($this->request->getUri(), 303);
                }

                // logout and enter new password to login
                $this->authenticationService->clearIdentity();

                $this->flashMessenger->addSuccess('Your account is deleted.', 'page-home');
                return new RedirectResponse($this->router->generateUri("page"));
            }
            $this->flashMessenger->addData('shouldRebind', true);
            $this->formsPlugin->saveState($profileDeleteForm);
            $this->flashMessenger->addError($this->formsPlugin->getMessages($profileDeleteForm), 'profile-delete');
            return new RedirectResponse($this->request->getUri(), 303);
        }

        return new HtmlResponse(
            $this->templateRenderer->render('user::profile', [
                'action' => 'delete-account',
                'content' => $this->templateRenderer->render('profile::delete-account', [
                    'form' => $profileDeleteForm
                ]),
            ])
        );
    }
}
