<?php

declare(strict_types=1);

namespace Frontend\User\Controller;

use Doctrine\ORM\NonUniqueResultException;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\Controller\AbstractActionController;
use Dot\DebugBar\DebugBar;
use Dot\FlashMessenger\FlashMessengerInterface;
use Exception;
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
use Psr\Http\Message\ResponseInterface;

use function sprintf;

class AccountController extends AbstractActionController
{
    protected RouterInterface $router;
    protected TemplateRendererInterface $template;
    protected UserServiceInterface $userService;
    protected AuthenticationServiceInterface $authenticationService;
    protected FlashMessengerInterface $messenger;
    protected FormsPlugin $forms;
    protected DebugBar $debugBar;

    /**
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
        TemplateRendererInterface $template,
        AuthenticationService $authenticationService,
        FlashMessengerInterface $messenger,
        FormsPlugin $forms,
        DebugBar $debugBar
    ) {
        $this->userService           = $userService;
        $this->router                = $router;
        $this->template              = $template;
        $this->authenticationService = $authenticationService;
        $this->messenger             = $messenger;
        $this->forms                 = $forms;
        $this->debugBar              = $debugBar;
    }

    public function activateAction(): ResponseInterface
    {
        $hash = $this->getRequest()->getAttribute('hash', false);
        if (! $hash) {
            $this->messenger->addError(sprintf(Message::MISSING_PARAMETER, 'hash'), 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        $user = $this->userService->findOneBy(['hash' => $hash]);
        if (! $user instanceof User) {
            $this->messenger->addError(Message::INVALID_ACTIVATION_CODE, 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        if ($user->getStatus() === User::STATUS_ACTIVE) {
            $this->messenger->addError(Message::USER_ALREADY_ACTIVATED, 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        try {
            $this->userService->activateUser($user);
            $this->debugBar->stackData();
        } catch (Exception $exception) {
            $this->messenger->addError($exception->getMessage(), 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        $this->messenger->addSuccess(Message::USER_ACTIVATED_SUCCESSFULLY, 'user-login');
        return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
    }

    public function unregisterAction(): ResponseInterface
    {
        $hash = $this->getRequest()->getAttribute('hash', false);
        if (! $hash) {
            $this->messenger->addError(sprintf(Message::MISSING_PARAMETER, 'hash'), 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        $user = $this->userService->findOneBy(['hash' => $hash]);
        if (! $user instanceof User) {
            $this->messenger->addError(Message::INVALID_ACTIVATION_CODE, 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        if ($user->getIsDeleted() === User::IS_DELETED_YES) {
            $this->messenger->addError(Message::USER_ALREADY_DEACTIVATED, 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        if ($user->getStatus() !== User::STATUS_PENDING) {
            $this->messenger->addError(Message::USER_UNREGISTER_STATUS, 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        try {
            $this->userService->updateUser($user, ['isDeleted' => User::IS_DELETED_YES]);
            $this->debugBar->stackData();
        } catch (Exception $exception) {
            $this->messenger->addError($exception->getMessage(), 'user-login');
            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        $this->messenger->addSuccess(Message::USER_DEACTIVATED_SUCCESSFULLY, 'user-login');
        return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
    }

    public function requestResetPasswordAction(): ResponseInterface
    {
        $form = new RequestResetPasswordForm();

        if (RequestMethodInterface::METHOD_POST === $this->getRequest()->getMethod()) {
            $form->setData($this->getRequest()->getParsedBody());
            if (! $form->isValid()) {
                $this->messenger->addError($this->forms->getMessages($form), 'request-reset');
                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            $user = $this->userService->findOneBy(['identity' => $form->getData()['identity']]);
            if (! $user instanceof User) {
                $this->messenger->addInfo(Message::MAIL_SENT_RESET_PASSWORD, 'request-reset');
                return new RedirectResponse($this->getRequest()->getUri());
            }

            try {
                $user = $this->userService->updateUser($user->createResetPassword());
                $this->debugBar->stackData();
            } catch (Exception $exception) {
                $this->messenger->addError($exception->getMessage(), 'request-reset');
                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            try {
                $this->userService->sendResetPasswordRequestedMail($user);
            } catch (Exception $exception) {
                $this->messenger->addError($exception->getMessage(), 'request-reset');
                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            $this->messenger->addInfo(Message::MAIL_SENT_RESET_PASSWORD, 'request-reset');
            return new RedirectResponse($this->getRequest()->getUri());
        }

        return new HtmlResponse(
            $this->template->render('user::request-reset-form', [
                'form' => $form,
            ])
        );
    }

    public function resetPasswordAction(): ResponseInterface
    {
        $form = new ResetPasswordForm();
        $hash = $this->getRequest()->getAttribute('hash') ?? null;
        if ($this->getRequest()->getMethod() === RequestMethodInterface::METHOD_POST) {
            $user = $this->userService->findByResetPasswordHash($hash);
            if (! $user instanceof User) {
                $this->messenger->addError(
                    sprintf(Message::RESET_PASSWORD_NOT_FOUND, $hash),
                    'reset-password'
                );

                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            /** @var UserResetPassword $resetPasswordRequest */
            $resetPasswordRequest = $user->getResetPasswords()->current();
            if (! $resetPasswordRequest->isValid()) {
                $this->messenger->addError(sprintf(Message::RESET_PASSWORD_EXPIRED, $hash), 'reset-password');

                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }
            if ($resetPasswordRequest->isCompleted()) {
                $this->messenger->addError(sprintf(Message::RESET_PASSWORD_USED, $hash), 'reset-password');

                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            $form->setData($this->getRequest()->getParsedBody());
            if (! $form->isValid()) {
                $this->messenger->addError($this->forms->getMessages($form), 'reset-password');

                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            try {
                $this->userService->updateUser(
                    $resetPasswordRequest->markAsCompleted()->getUser(),
                    $form->getData()
                );
                $this->debugBar->stackData();
            } catch (Exception $exception) {
                $this->messenger->addError($exception->getMessage(), 'reset-password');

                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            try {
                $this->userService->sendResetPasswordCompletedMail($user);
            } catch (Exception $exception) {
                $this->messenger->addError($exception->getMessage(), 'reset-password');

                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }
            $this->messenger->addSuccess(Message::PASSWORD_RESET_SUCCESSFULLY, 'user-login');

            return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
        }

        return new HtmlResponse(
            $this->template->render('user::reset-password-form', [
                'form' => $form,
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
        $form = new UploadAvatarForm();
        if (RequestMethodInterface::METHOD_POST === $this->request->getMethod()) {
            $file = $this->request->getUploadedFiles()['avatar']['image'] ?? '';
            if ($file->getSize() === 0) {
                $this->messenger->addWarning('Please select a file for upload.', 'profile-avatar');
                return new RedirectResponse($this->router->generateUri(
                    "account",
                    ['action' => 'avatar']
                ));
            }

            try {
                $this->userService->updateUser($user, ['avatar' => $file]);
                $this->debugBar->stackData();
            } catch (Exception) {
                $this->messenger->addError('Something went wrong updating your profile image!', 'profile-avatar');
                return new RedirectResponse($this->router->generateUri(
                    "account",
                    ['action' => 'avatar']
                ));
            }
            $this->messenger->addSuccess('Profile image updated successfully!', 'profile-avatar');
            return new RedirectResponse($this->router->generateUri(
                "account",
                ['action' => 'avatar']
            ));
        }

        return new HtmlResponse(
            $this->template->render('user::profile', [
                'action'  => 'avatar',
                'content' => $this->template->render('profile::avatar', [
                    'user' => $user,
                    'form' => $form,
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
        $form = new ProfileDetailsForm();

        $shouldRebind = $this->messenger->getData('shouldRebind') ?? true;
        if ($shouldRebind) {
            $this->forms->restoreState($form);
        }

        if (RequestMethodInterface::METHOD_POST === $this->request->getMethod()) {
            $form->setData($this->request->getParsedBody());
            if ($form->isValid()) {
                $userData = $form->getData();
                try {
                    $this->userService->updateUser($user, $userData);
                    $this->debugBar->stackData();
                } catch (Exception $e) {
                    $this->messenger->addData('shouldRebind', true);
                    $this->forms->saveState($form);
                    $this->messenger->addError($e->getMessage(), 'profile-details');

                    return new RedirectResponse($this->request->getUri(), 303);
                }

                $this->messenger->addSuccess('Profile details updated.', 'profile-details');
                return new RedirectResponse($this->router->generateUri(
                    "account",
                    ['action' => 'details']
                ));
            } else {
                $this->messenger->addData('shouldRebind', true);
                $this->forms->saveState($form);
                $this->messenger->addError($this->forms->getMessages($form), 'profile-details');

                return new RedirectResponse($this->request->getUri(), 303);
            }
        } else {
            if ($user instanceof User) {
                $userDetails['detail']['firstName'] = $user->getDetail()->getFirstName();
                $userDetails['detail']['lastName']  = $user->getDetail()->getLastName();
                $form->setData($userDetails);
            } else {
                $this->authenticationService->clearIdentity();
                return new RedirectResponse(
                    $this->router->generateUri('page')
                );
            }
        }

        return new HtmlResponse(
            $this->template->render('user::profile', [
                'action'  => 'details',
                'content' => $this->template->render('profile::details', [
                    'form' => $form,
                ]),
            ])
        );
    }

    public function changePasswordAction(): ResponseInterface
    {
        /** @var UserIdentity $identity */
        $identity = $this->authenticationService->getIdentity();

        $user = $this->userService->findByUuid($identity->getUuid());

        $form = new ProfilePasswordForm();

        $shouldRebind = $this->messenger->getData('shouldRebind') ?? true;
        if ($shouldRebind) {
            $this->forms->restoreState($form);
        }

        if (RequestMethodInterface::METHOD_POST === $this->request->getMethod()) {
            $form->setData($this->request->getParsedBody());
            if ($form->isValid()) {
                $userData = $form->getData();
                try {
                    $this->userService->updateUser($user, $userData);
                    $this->debugBar->stackData();
                } catch (Exception $e) {
                    $this->messenger->addData('shouldRebind', true);
                    $this->forms->saveState($form);
                    $this->messenger->addError($e->getMessage(), 'profile-password');

                    return new RedirectResponse($this->request->getUri(), 303);
                }

                // logout and enter new password to login
                $this->authenticationService->clearIdentity();

                $this->messenger->addSuccess('Password updated. Login with your new credentials.', 'user-login');
                return new RedirectResponse($this->router->generateUri("user", ['action' => 'login']));
            } else {
                $this->messenger->addData('shouldRebind', true);
                $this->forms->saveState($form);
                $this->messenger->addError($this->forms->getMessages($form), 'profile-password');

                return new RedirectResponse($this->request->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->template->render('user::profile', [
                'action'  => 'change-password',
                'content' => $this->template->render('profile::change-password', [
                    'form' => $form,
                ]),
            ])
        );
    }

    public function deleteAccountAction(): ResponseInterface
    {
        /** @var UserIdentity $identity */
        $identity = $this->authenticationService->getIdentity();

        $user = $this->userService->findByUuid($identity->getUuid());

        $form = new ProfileDeleteForm();

        $shouldRebind = $this->messenger->getData('shouldRebind') ?? true;
        if ($shouldRebind) {
            $this->forms->restoreState($form);
        }

        if (RequestMethodInterface::METHOD_POST === $this->request->getMethod()) {
            $form->setData($this->request->getParsedBody());
            if ($form->isValid()) {
                $userData = $form->getData();
                try {
                    $this->userService->updateUser($user, $userData);
                    $this->debugBar->stackData();
                } catch (Exception $e) {
                    $this->messenger->addData('shouldRebind', true);
                    $this->forms->saveState($form);
                    $this->messenger->addError($e->getMessage(), 'profile-delete');

                    return new RedirectResponse($this->request->getUri(), 303);
                }

                // logout and enter new password to login
                $this->authenticationService->clearIdentity();

                $this->messenger->addSuccess('Your account is deleted.', 'page-home');
                return new RedirectResponse($this->router->generateUri("page"));
            } else {
                $this->messenger->addData('shouldRebind', true);
                $this->forms->saveState($form);
                $this->messenger->addError($this->forms->getMessages($form), 'profile-delete');

                return new RedirectResponse($this->request->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->template->render('user::profile', [
                'action'  => 'delete-account',
                'content' => $this->template->render('profile::delete-account', [
                    'form' => $form,
                ]),
            ])
        );
    }
}
