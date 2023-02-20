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
class UserController extends AbstractActionController
{
    protected CookieServiceInterface $cookieService;
    protected RouterInterface $router;
    protected TemplateRendererInterface $template;
    protected UserServiceInterface $userService;
    protected AuthenticationService $authenticationService;
    protected FlashMessengerInterface $messenger;
    protected FormsPlugin $forms;
    protected DebugBar $debugBar;
    protected array $config = [];

    /**
     * UserController constructor.
     * @param CookieServiceInterface $cookieService
     * @param UserServiceInterface $userService
     * @param RouterInterface $router
     * @param TemplateRendererInterface $template
     * @param AuthenticationService $authenticationService
     * @param FlashMessengerInterface $messenger
     * @param FormsPlugin $forms
     * @param DebugBar $debugBar
     * @param array $config
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
        TemplateRendererInterface $template,
        AuthenticationService $authenticationService,
        FlashMessengerInterface $messenger,
        FormsPlugin $forms,
        DebugBar $debugBar,
        array $config = []
    ) {
        $this->cookieService = $cookieService;
        $this->userService = $userService;
        $this->router = $router;
        $this->template = $template;
        $this->authenticationService = $authenticationService;
        $this->messenger = $messenger;
        $this->forms = $forms;
        $this->debugBar = $debugBar;
        $this->config = $config;
    }

    /**
     * @return ResponseInterface
     * @throws NonUniqueResultException
     * @throws Exception
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

    /**
     * @return ResponseInterface
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

    /**
     * @return ResponseInterface
     */
    public function registerAction(): ResponseInterface
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
