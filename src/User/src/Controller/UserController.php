<?php

declare(strict_types=1);

namespace Frontend\User\Controller;

use Doctrine\ORM\NonUniqueResultException;
use Dot\Controller\AbstractActionController;
use Dot\DebugBar\DebugBar;
use Dot\FlashMessenger\FlashMessengerInterface;
use Fig\Http\Message\RequestMethodInterface;
use Frontend\App\Service\CookieServiceInterface;
use Frontend\Plugin\FormsPlugin;
use Frontend\User\Adapter\AuthenticationAdapter;
use Frontend\User\Entity\User;
use Frontend\User\Form\LoginForm;
use Frontend\User\Form\RegisterForm;
use Frontend\User\Service\UserServiceInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Dot\AnnotatedServices\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;
use Exception;

/**
 * Class UserController
 * @package Frontend\User\Controller
 */
final class UserController extends AbstractActionController
{
    private readonly CookieServiceInterface $cookieService;
    private readonly RouterInterface $router;
    private readonly TemplateRendererInterface $templateRenderer;
    private readonly UserServiceInterface $userService;
    private readonly AuthenticationService $authenticationService;
    private readonly FlashMessengerInterface $flashMessenger;
    private readonly FormsPlugin $formsPlugin;
    private readonly DebugBar $debugBar;
    private array $config = [];

    /**
     * UserController constructor.
     *
     * @Inject({
     *     CookieServiceInterface::class,
     *     UserServiceInterface::class,
     *     RouterInterface::class,
     *     TemplateRendererInterface::class,
     *     AuthenticationService::class,
     *     FlashMessengerInterface::class,
     *     FormsPlugin::class,
     *     DebugBar::class,
     *     "config"
     * })
     */
    public function __construct(
        CookieServiceInterface $cookieService,
        UserServiceInterface $userService,
        RouterInterface $router,
        TemplateRendererInterface $templateRenderer,
        AuthenticationService $authenticationService,
        FlashMessengerInterface $flashMessenger,
        FormsPlugin $formsPlugin,
        DebugBar $debugBar,
        array $config = []
    ) {
        $this->cookieService = $cookieService;
        $this->userService = $userService;
        $this->router = $router;
        $this->templateRenderer = $templateRenderer;
        $this->authenticationService = $authenticationService;
        $this->flashMessenger = $flashMessenger;
        $this->formsPlugin = $formsPlugin;
        $this->debugBar = $debugBar;
        $this->config = $config;
    }

    /**
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function loginAction(): ResponseInterface
    {
        if ($this->authenticationService->hasIdentity()) {
            return new RedirectResponse($this->router->generateUri("page"));
        }

        $loginForm = new LoginForm();

        $shouldRebind = $this->flashMessenger->getData('shouldRebind') ?? true;
        if ($shouldRebind) {
            $this->formsPlugin->restoreState($loginForm);
        }

        if (RequestMethodInterface::METHOD_POST === $this->getRequest()->getMethod()) {
            $loginForm->setData($this->getRequest()->getParsedBody());
            if ($loginForm->isValid()) {
                /** @var AuthenticationAdapter $adapter */
                $adapter = $this->authenticationService->getAdapter();
                $data = $loginForm->getData();
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
                }
                $this->flashMessenger->addData('shouldRebind', true);
                $this->formsPlugin->saveState($loginForm);
                $this->flashMessenger->addError($authResult->getMessages(), 'user-login');
                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }
            $this->flashMessenger->addData('shouldRebind', true);
            $this->formsPlugin->saveState($loginForm);
            $this->flashMessenger->addError($this->formsPlugin->getMessages($loginForm), 'user-login');
            return new RedirectResponse($this->getRequest()->getUri(), 303);
        }

        return new HtmlResponse(
            $this->templateRenderer->render('user::login', [
                'form' => $loginForm
            ])
        );
    }

    /**
     * @throws NonUniqueResultException
     */
    public function logoutAction(): ResponseInterface
    {
        $this->userService->deleteExpiredRememberMeTokens();

        $this->userService->deleteRememberMeToken(
            $this->request->getCookieParams()
        );

        $this->cookieService->expireCookie($this->config['rememberMe']['cookie']['name']);

        $this->authenticationService->clearIdentity();

        return new RedirectResponse(
            $this->router->generateUri('page')
        );
    }

    public function registerAction(): ResponseInterface
    {
        if ($this->authenticationService->hasIdentity()) {
            return new RedirectResponse($this->router->generateUri("page"));
        }

        $registerForm = new RegisterForm();

        $shouldRebind = $this->flashMessenger->getData('shouldRebind') ?? true;
        if ($shouldRebind) {
            $this->formsPlugin->restoreState($registerForm);
        }

        if (RequestMethodInterface::METHOD_POST === $this->getRequest()->getMethod()) {
            $registerForm->setData($this->getRequest()->getParsedBody());
            if ($registerForm->isValid()) {
                $userData = $registerForm->getData();
                try {
                    /** @var User $user */
                    $user = $this->userService->createUser($userData);
                    $this->debugBar->stackData();
                } catch (Exception $exception) {
                    $this->flashMessenger->addData('shouldRebind', true);
                    $this->formsPlugin->saveState($registerForm);
                    $this->flashMessenger->addError($exception->getMessage(), 'user-register');

                    return new RedirectResponse($this->getRequest()->getUri(), 303);
                }

                try {
                    $this->userService->sendActivationMail($user);
                    $this->flashMessenger->addSuccess('Check the email to activate your account.', 'user-login');

                    return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
                } catch (Exception $exception) {
                    $this->flashMessenger->addError($exception->getMessage(), 'user-login');
                    return new RedirectResponse($this->getRequest()->getUri(), 303);
                }
            } else {
                $this->flashMessenger->addData('shouldRebind', true);
                $this->formsPlugin->saveState($registerForm);
                $this->flashMessenger->addError($this->formsPlugin->getMessages($registerForm), 'user-register');

                return new RedirectResponse($this->getRequest()->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->templateRenderer->render('user::register', [
                'form' => $registerForm
            ])
        );
    }
}
