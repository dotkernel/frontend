<?php

declare(strict_types=1);

namespace Frontend\User\Controller;

use Dot\Controller\AbstractActionController;
use Dot\DependencyInjection\Attribute\Inject;
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
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Form\FieldsetInterface;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

use function array_merge;
use function sprintf;

class AccountController extends AbstractActionController
{
    #[Inject(
        UserServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        AuthenticationService::class,
        FlashMessengerInterface::class,
        FormsPlugin::class,
    )]
    public function __construct(
        protected UserServiceInterface $userService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected AuthenticationService $authenticationService,
        protected FlashMessengerInterface $messenger,
        protected FormsPlugin $forms,
    ) {
    }

    public function activateAction(): ResponseInterface
    {
        $hash = $this->getRequest()->getAttribute('hash', false);
        if (! $hash) {
            $this->messenger->addError(sprintf(Message::MISSING_PARAMETER, 'hash'), 'user-login');
            return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
        }

        $user = $this->userService->findOneBy(['hash' => $hash]);
        if (! $user instanceof User) {
            $this->messenger->addError(Message::INVALID_ACTIVATION_CODE, 'user-login');
            return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
        }

        if ($user->isActive()) {
            $this->messenger->addError(Message::USER_ALREADY_ACTIVATED, 'user-login');
            return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
        }

        try {
            $this->userService->activateUser($user);
        } catch (Exception $exception) {
            $this->messenger->addError($exception->getMessage(), 'user-login');
            return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
        }

        $this->messenger->addSuccess(Message::USER_ACTIVATED_SUCCESSFULLY, 'user-login');
        return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
    }

    public function unregisterAction(): ResponseInterface
    {
        $hash = $this->getRequest()->getAttribute('hash', false);
        if (! $hash) {
            $this->messenger->addError(sprintf(Message::MISSING_PARAMETER, 'hash'), 'user-login');
            return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
        }

        $user = $this->userService->findOneBy(['hash' => $hash]);
        if (! $user instanceof User) {
            $this->messenger->addError(Message::INVALID_ACTIVATION_CODE, 'user-login');
            return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
        }

        if ($user->getIsDeleted() === User::IS_DELETED_YES) {
            $this->messenger->addError(Message::USER_ALREADY_DEACTIVATED, 'user-login');
            return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
        }

        if (! $user->isPending()) {
            $this->messenger->addError(Message::USER_UNREGISTER_STATUS, 'user-login');
            return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
        }

        try {
            $this->userService->updateUser($user, ['isDeleted' => User::IS_DELETED_YES]);
        } catch (Exception $exception) {
            $this->messenger->addError($exception->getMessage(), 'user-login');
            return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
        }

        $this->messenger->addSuccess(Message::USER_DEACTIVATED_SUCCESSFULLY, 'user-login');
        return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
    }

    public function requestResetPasswordAction(): ResponseInterface
    {
        $form = new RequestResetPasswordForm();
        $form->setAttribute('action', $this->router->generateUri('account', ['action' => 'request-reset-password']));

        if (RequestMethodInterface::METHOD_POST === $this->getRequest()->getMethod()) {
            $form->setData($this->getRequest()->getParsedBody());
            if (! $form->isValid()) {
                $this->messenger->addError($this->forms->getMessages($form), 'request-reset');
                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }

            /** @var array $data */
            $data = $form->getData();
            $user = $this->userService->findOneBy(['identity' => $data['identity']]);
            if (! $user instanceof User) {
                $this->messenger->addInfo(Message::MAIL_SENT_RESET_PASSWORD, 'request-reset');
                return new RedirectResponse($this->getRequest()->getUri());
            }

            try {
                $user = $this->userService->updateUser($user->createResetPassword());
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

        $form->setAttribute('action', $this->router->generateUri(
            'account',
            [
                'action' => 'reset-password',
                'hash'   => $hash,
            ]
        ));

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

            /** @var array $data */
            $data = $form->getData();
            try {
                $this->userService->updateUser(
                    $resetPasswordRequest->markAsCompleted()->getUser(),
                    $data
                );
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

            return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
        }

        return new HtmlResponse(
            $this->template->render('user::reset-password-form', [
                'form' => $form,
            ])
        );
    }

    public function avatarAction(): ResponseInterface
    {
        /** @var UserIdentity $identity */
        $identity = $this->authenticationService->getIdentity();

        $user = $this->userService->findByUuid($identity->getUuid());

        $form = new UploadAvatarForm();
        $form->setAttribute('action', $this->router->generateUri('account', ['action' => 'avatar']));

        /** @var FieldsetInterface $avatarElement */
        $avatarElement = $form->get('avatar');
        $imageElement  = $avatarElement->get('image');
        $imageElement->setAttribute('data-url', $this->router->generateUri('account', ['action' => 'avatar']));
        $imageElement->setAttribute(
            'data-preview',
            $user->getAvatar()?->getUrl() ?? '/images/app/user/user-placeholder.png'
        );

        if (RequestMethodInterface::METHOD_POST === $this->request->getMethod()) {
            $form->setData(array_merge($this->request->getParsedBody(), $this->request->getUploadedFiles()));
            if ($form->isValid()) {
                try {
                    $this->userService->updateUser($user, [
                        'avatar' => $this->request->getUploadedFiles()['avatar']['image'],
                    ]);
                } catch (Exception) {
                    $this->messenger->addError('Something went wrong updating your profile image!', 'profile-avatar');
                    return new RedirectResponse($this->router->generateUri('account', ['action' => 'avatar']));
                }
                $this->messenger->addSuccess('Profile image updated successfully!', 'profile-avatar');
            } else {
                $this->messenger->addError($this->forms->getMessages($form), 'profile-avatar');
            }

            return new RedirectResponse($this->router->generateUri('account', ['action' => 'avatar']));
        }

        return new HtmlResponse(
            $this->template->render('user::profile', [
                'action'  => 'avatar',
                'content' => $this->template->render('profile::avatar', [
                    'user' => $user,
                    'form' => $form->prepare(),
                ]),
            ])
        );
    }

    public function detailsAction(): ResponseInterface
    {
        /** @var UserIdentity $identity */
        $identity = $this->authenticationService->getIdentity();

        $user = $this->userService->findByUuid($identity->getUuid());
        $form = new ProfileDetailsForm();
        $form->setAttribute('action', $this->router->generateUri('account', ['action' => 'details']));

        $shouldRebind = $this->messenger->getData('shouldRebind') ?? true;
        if ($shouldRebind) {
            $this->forms->restoreState($form);
        }

        if (RequestMethodInterface::METHOD_POST === $this->request->getMethod()) {
            $form->setData($this->request->getParsedBody());
            if ($form->isValid()) {
                /** @var array $userData */
                $userData = $form->getData();
                try {
                    $this->userService->updateUser($user, $userData);
                } catch (Exception $e) {
                    $this->messenger->addData('shouldRebind', true);
                    $this->forms->saveState($form);
                    $this->messenger->addError($e->getMessage(), 'profile-details');

                    return new RedirectResponse($this->request->getUri(), 303);
                }

                $this->messenger->addSuccess('Profile details updated.', 'profile-details');
                return new RedirectResponse($this->router->generateUri('account', ['action' => 'details']));
            } else {
                $this->messenger->addData('shouldRebind', true);
                $this->forms->saveState($form);
                $this->messenger->addError($this->forms->getMessages($form), 'profile-details');

                return new RedirectResponse($this->request->getUri(), 303);
            }
        } else {
            if ($user instanceof User) {
                $form->setData([
                    'detail' => [
                        'firstName' => $user->getDetail()?->getFirstName(),
                        'lastName'  => $user->getDetail()?->getLastName(),
                    ],
                ]);
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
                    'form' => $form->prepare(),
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
        $form->setAttribute('action', $this->router->generateUri('account', ['action' => 'change-password']));

        $shouldRebind = $this->messenger->getData('shouldRebind') ?? true;
        if ($shouldRebind) {
            $this->forms->restoreState($form);
        }

        if (RequestMethodInterface::METHOD_POST === $this->request->getMethod()) {
            $form->setData($this->request->getParsedBody());
            if ($form->isValid()) {
                /** @var array $userData */
                $userData = $form->getData();
                try {
                    $this->userService->updateUser($user, $userData);
                } catch (Exception $e) {
                    $this->messenger->addData('shouldRebind', true);
                    $this->forms->saveState($form);
                    $this->messenger->addError($e->getMessage(), 'profile-password');

                    return new RedirectResponse($this->request->getUri(), 303);
                }

                // logout and enter new password to login
                $this->authenticationService->clearIdentity();

                $this->messenger->addSuccess('Password updated. Login with your new credentials.', 'user-login');
                return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
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
                    'form' => $form->prepare(),
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
        $form->setAttribute('action', $this->router->generateUri('account', ['action' => 'delete-account']));

        $shouldRebind = $this->messenger->getData('shouldRebind') ?? true;
        if ($shouldRebind) {
            $this->forms->restoreState($form);
        }

        if (RequestMethodInterface::METHOD_POST === $this->request->getMethod()) {
            $form->setData($this->request->getParsedBody());
            if ($form->isValid()) {
                /** @var array $userData */
                $userData = $form->getData();
                try {
                    $this->userService->updateUser($user, $userData);
                    $this->userService->deleteAvatar($user);
                } catch (Exception $e) {
                    $this->messenger->addData('shouldRebind', true);
                    $this->forms->saveState($form);
                    $this->messenger->addError($e->getMessage(), 'profile-delete');

                    return new RedirectResponse($this->request->getUri(), 303);
                }

                // logout and enter new password to login
                $this->authenticationService->clearIdentity();

                $this->messenger->addSuccess(Message::ACCOUNT_IS_DELETED, 'page-home');
                return new RedirectResponse($this->router->generateUri('page'));
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
                    'form' => $form->prepare(),
                ]),
            ])
        );
    }
}
