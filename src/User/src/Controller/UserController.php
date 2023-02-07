<?php

declare(strict_types=1);

namespace Frontend\User\Controller;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Dot\Controller\AbstractActionController;
use Dot\DebugBar\DebugBar;
use Dot\FlashMessenger\FlashMessenger;
use Fig\Http\Message\RequestMethodInterface;
use Frontend\Plugin\FormsPlugin;
use Frontend\User\Authentication\AuthenticationAdapter;
use Frontend\User\Entity\User;
use Frontend\User\Form\LoginForm;
use Frontend\User\Form\RegisterForm;
use Frontend\User\Service\UserService;
use Laminas\Authentication\AuthenticationService;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Dot\AnnotatedServices\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;
use Exception;

class UserController extends AbstractActionController
{
    /** @var RouterInterface $router */
    protected RouterInterface $router;

    /** @var TemplateRendererInterface $template */
    protected TemplateRendererInterface $template;

    /** @var UserService $userService */
    protected UserService $userService;

    /** @var AuthenticationService $authenticationService */
    protected AuthenticationService $authenticationService;

    /** @var FlashMessenger $messenger */
    protected FlashMessenger $messenger;

    /** @var FormsPlugin $forms */
    protected FormsPlugin $forms;

    /** @var DebugBar $debugBar */
    protected DebugBar $debugBar;

    /**
     * UserController constructor.
     * @param UserService $userService
     * @param RouterInterface $router
     * @param TemplateRendererInterface $template
     * @param AuthenticationService $authenticationService
     * @param FlashMessenger $messenger
     * @param FormsPlugin $forms
     * @param DebugBar $debugBar
     * @Inject({
     *     UserService::class,
     *     RouterInterface::class,
     *     TemplateRendererInterface::class,
     *     AuthenticationService::class,
     *     FlashMessenger::class,
     *     FormsPlugin::class,
     *     DebugBar::class
     * })
     */
    public function __construct(
        UserService $userService,
        RouterInterface $router,
        TemplateRendererInterface $template,
        AuthenticationService $authenticationService,
        FlashMessenger $messenger,
        FormsPlugin $forms,
        DebugBar $debugBar
    ) {
        $this->userService = $userService;
        $this->router = $router;
        $this->template = $template;
        $this->authenticationService = $authenticationService;
        $this->messenger = $messenger;
        $this->forms = $forms;
        $this->debugBar = $debugBar;
    }

    /**
     * @return ResponseInterface
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function loginAction(): ResponseInterface
    {
        if ($this->authenticationService->hasIdentity()) {
            return new RedirectResponse($this->router->generateUri("page"));
        }

        $form = new LoginForm();

        $shouldRebind = $this->messenger->getData('shouldRebind') ?? true;
        if ($shouldRebind) {
            $this->forms->restoreState($form);
        }

        if (RequestMethodInterface::METHOD_POST === $this->getRequest()->getMethod()) {
            $form->setData($this->getRequest()->getParsedBody());
            if ($form->isValid()) {
                /** @var AuthenticationAdapter $adapter */
                $adapter = $this->authenticationService->getAdapter();
                $data = $form->getData();
                $adapter->setIdentity($data['identity'])->setCredential($data['password']);
                $authResult = $this->authenticationService->authenticate();
                if ($authResult->isValid()) {
                    $identity = $authResult->getIdentity();
                    $user = $this->userService->findByIdentity($identity->getIdentity());
                    $deviceType = $this->getRequest()->getServerParams();
                    $this->authenticationService->getStorage()->write($identity);
                    if (!empty($data['rememberMe'])) {
                        $this->userService->addRememberMeToken(
                            $user,
                            $deviceType['HTTP_USER_AGENT'],
                            $this->request->getCookieParams()
                        );
                    }
                    return new RedirectResponse($this->router->generateUri("page"));
                } else {
                    $this->messenger->addData('shouldRebind', true);
                    $this->forms->saveState($form);
                    $this->messenger->addError($authResult->getMessages(), 'user-login');
                    return new RedirectResponse($this->getRequest()->getUri(), 303);
                }
            } else {
                $this->messenger->addData('shouldRebind', true);
                $this->forms->saveState($form);
                $this->messenger->addError($this->forms->getMessages($form), 'user-login');
                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->template->render('user::login', [
                'form' => $form
            ])
        );
    }

    public function logoutAction(): ResponseInterface
    {
        if (!empty($_COOKIE['rememberMe'])) {
            $this->userService->deleteRememberMeCookie();
        }
        $this->authenticationService->clearIdentity();
        return new RedirectResponse(
            $this->router->generateUri('page')
        );
    }

    public function registerAction()
    {
        if ($this->authenticationService->hasIdentity()) {
            return new RedirectResponse($this->router->generateUri("page"));
        }

        $form = new RegisterForm();

        $shouldRebind = $this->messenger->getData('shouldRebind') ?? true;
        if ($shouldRebind) {
            $this->forms->restoreState($form);
        }

        if (RequestMethodInterface::METHOD_POST === $this->getRequest()->getMethod()) {
            $form->setData($this->getRequest()->getParsedBody());
            if ($form->isValid()) {
                $userData = $form->getData();
                try {
                    /** @var User $user */
                    $user = $this->userService->createUser($userData);
                    $this->debugBar->stackData();
                } catch (Exception $e) {
                    $this->messenger->addData('shouldRebind', true);
                    $this->forms->saveState($form);
                    $this->messenger->addError($e->getMessage(), 'user-register');

                    return new RedirectResponse($this->getRequest()->getUri(), 303);
                }

                try {
                    $this->userService->sendActivationMail($user);
                    $this->messenger->addSuccess('Check the email to activate your account.', 'user-login');

                    return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
                } catch (Exception $exception) {
                    $this->messenger->addError($exception->getMessage(), 'user-login');
                    return new RedirectResponse($this->getRequest()->getUri(), 303);
                }
            } else {
                $this->messenger->addData('shouldRebind', true);
                $this->forms->saveState($form);
                $this->messenger->addError($this->forms->getMessages($form), 'user-register');

                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->template->render('user::register', [
                'form' => $form
            ])
        );
    }
}
