<?php

declare(strict_types=1);

namespace Frontend\User\Handler;

use Frontend\User\Service\UserService;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\FlashMessenger\FlashMessenger;
use Fig\Http\Message\RequestMethodInterface;
use Frontend\App\Handler\AbstractHandler;
use Frontend\Plugin\FormsPlugin;
use Frontend\User\Form\ProfileDeleteForm;
use Frontend\User\Form\ProfileDetailsForm;
use Frontend\User\Form\ProfilePasswordForm;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class ProfileHandler
 * @package Frontend\User\Handler
 */
class ProfileHandler extends AbstractHandler
{
    /** @var RouterInterface $router */
    protected $router;

    /** @var UserService $userService */
    protected $userService;

    /** @var TemplateRendererInterface $template */
    protected $template;

    /** @var FlashMessenger $messenger */
    protected $messenger;

    /** @var FormsPlugin $forms */
    protected $forms;

    /** @var AuthenticationServiceInterface $authenticationService */
    protected $authenticationService;

    /**
     * ProfileHandler constructor.
     * @param RouterInterface $router
     * @param UserService $userService
     * @param TemplateRendererInterface $template
     * @param FlashMessenger $messenger
     * @param FormsPlugin $forms
     * @param AuthenticationService $authenticationService
     *
     * @Inject({RouterInterface::class, UserService::class, TemplateRendererInterface::class,
     *     FlashMessenger::class, FormsPlugin::class, AuthenticationService::class})
     */
    public function __construct(
        RouterInterface $router,
        UserService $userService,
        TemplateRendererInterface $template,
        FlashMessenger $messenger,
        FormsPlugin $forms,
        AuthenticationService $authenticationService
    ) {
        $this->router = $router;
        $this->userService = $userService;
        $this->template = $template;
        $this->messenger = $messenger;
        $this->forms = $forms;
        $this->authenticationService = $authenticationService;
    }

    /**
     * @return ResponseInterface
     */
    public function indexAction(): ResponseInterface
    {
        return new RedirectResponse($this->router->generateUri("profile.get-post", ['action' => 'details']));
    }

    /**
     * @return ResponseInterface
     * @throws \Doctrine\ORM\ORMException
     */
    public function avatarAction(): ResponseInterface
    {
        $user = $this->getIdentity();

        if (RequestMethodInterface::METHOD_POST === $this->request->getMethod()) {
            $file = $this->request->getUploadedFiles()['image'] ?? '';

            if (!$file instanceof UploadedFileInterface) {
                $this->messenger->addSuccess('Please select a file for upload.', 'profile-avatar');
            }

            $user = $this->userService->updateUser($user, ['avatar' => $file]);

            return new JsonResponse([
                'ok' => 'Profile image updated successfully!',
                'imageUrl' => $user->getAvatar()->getUrl(),
            ]);
        }

        return new HtmlResponse(
            $this->template->render('user::profile', [
                'action' => 'avatar',
                'content' => $this->template->render('profile::avatar', [
                    'userUploadsBaseUrl' => 'http://localhost:8080/uploads/user/',
                    'user' => $user
                ]),
            ])
        );
    }

    /**
     * @return ResponseInterface
     */
    public function detailsAction(): ResponseInterface
    {
        $userDetails = [];
        $user = $this->getIdentity();
        if (!empty($user)) {
            $userDetails['detail']['firstName'] = $user->getDetail()->getFirstName();
            $userDetails['detail']['lastName'] = $user->getDetail()->getLastName();
        }
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
                } catch (\Exception $e) {
                    $this->messenger->addData('shouldRebind', true);
                    $this->forms->saveState($form);
                    $this->messenger->addError($e->getMessage(), 'profile-details');

                    return new RedirectResponse($this->request->getUri(), 303);
                }

                $this->messenger->addSuccess('Profile details updated.', 'profile-details');
                return new RedirectResponse($this->router->generateUri(
                    "profile.get-post",
                    ['action' => 'details']
                ));
            } else {
                $this->messenger->addData('shouldRebind', true);
                $this->forms->saveState($form);
                $this->messenger->addError($this->forms->getMessages($form), 'profile-details');

                return new RedirectResponse($this->request->getUri(), 303);
            }
        } else {
            $form->setData($userDetails);
        }

        return new HtmlResponse(
            $this->template->render('user::profile', [
                'action' => 'details',
                'content' => $this->template->render('profile::details', [
                    'form' => $form
                ]),
            ])
        );
    }

    /**
     * @return ResponseInterface
     */
    public function changePasswordAction(): ResponseInterface
    {
        $user = $this->getIdentity();

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
                } catch (\Exception $e) {
                    $this->messenger->addData('shouldRebind', true);
                    $this->forms->saveState($form);
                    $this->messenger->addError($e->getMessage(), 'profile-password');

                    return new RedirectResponse($this->request->getUri(), 303);
                }

                // logout and enter new password to login
                $this->authenticationService->clearIdentity();

                $this->messenger->addSuccess('Password updated. Login with your new credentials.', 'user-login');
                return new RedirectResponse($this->router->generateUri("user.login"));
            } else {
                $this->messenger->addData('shouldRebind', true);
                $this->forms->saveState($form);
                $this->messenger->addError($this->forms->getMessages($form), 'profile-password');

                return new RedirectResponse($this->request->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->template->render('user::profile', [
                'action' => 'change-password',
                'content' => $this->template->render('profile::change-password', [
                    'form' => $form
                ]),
            ])
        );
    }

    /**
     * @return ResponseInterface
     */
    public function deleteAccountAction(): ResponseInterface
    {
        $user = $this->getIdentity();

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
                } catch (\Exception $e) {
                    $this->messenger->addData('shouldRebind', true);
                    $this->forms->saveState($form);
                    $this->messenger->addError($e->getMessage(), 'profile-delete');

                    return new RedirectResponse($this->request->getUri(), 303);
                }

                // logout and enter new password to login
                $this->authenticationService->clearIdentity();

                $this->messenger->addSuccess('Your account is deleted.', 'page-home');
                return new RedirectResponse($this->router->generateUri("page.home"));
            } else {
                $this->messenger->addData('shouldRebind', true);
                $this->forms->saveState($form);
                $this->messenger->addError($this->forms->getMessages($form), 'profile-delete');

                return new RedirectResponse($this->request->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->template->render('user::profile', [
                'action' => 'delete-account',
                'content' => $this->template->render('profile::delete-account', [
                    'form' => $form
                ]),
            ])
        );
    }
}
