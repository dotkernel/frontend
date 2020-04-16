<?php

declare(strict_types=1);

namespace Frontend\User\Handler;

use Dot\AnnotatedServices\Annotation\Inject;
use Frontend\User\Service\UserService;
use Dot\FlashMessenger\FlashMessenger;
use Fig\Http\Message\RequestMethodInterface;
use Frontend\Plugin\FlashMessengerPlugin;
use Frontend\Plugin\FormsPlugin;
use Frontend\User\Form\LoginForm;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Form\Form;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class LoginHandler
 * @package Frontend\User\Handler
 */
class LoginHandler implements RequestHandlerInterface
{
    /** @var RouterInterface $router */
    protected $router;

    /** @var TemplateRendererInterface $template */
    protected $template;

    /** @var UserService $userService */
    protected $userService;

    /** @var AuthenticationServiceInterface $authenticationService */
    protected $authenticationService;

    /** @var FlashMessenger $messenger */
    protected $messenger;

    /** @var FormsPlugin $forms */
    protected $forms;

    /**
     * LoginHandler constructor.
     * @param RouterInterface $router
     * @param TemplateRendererInterface $template
     * @param UserService $userService
     * @param AuthenticationService $authenticationService
     * @param FlashMessenger $messenger
     * @param FormsPlugin $forms
     *
     * @Inject({RouterInterface::class, TemplateRendererInterface::class, AuthenticationService::class,
     *      UserService::class, FlashMessenger::class, FormsPlugin::class})
     */
    public function __construct(
        RouterInterface $router,
        TemplateRendererInterface $template,
        AuthenticationService $authenticationService,
        UserService $userService,
        FlashMessenger $messenger,
        FormsPlugin $forms
    ) {
        $this->router = $router;
        $this->template = $template;
        $this->userService = $userService;
        $this->authenticationService = $authenticationService;
        $this->messenger = $messenger;
        $this->forms = $forms;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->authenticationService->hasIdentity()) {
            return new RedirectResponse($this->router->generateUri("page.home"));
        }

        $form = new LoginForm();

        $shouldRebind = $this->messenger->getData('shouldRebind') ?? true;
        if ($shouldRebind) {
            $this->forms->restoreState($form);
        }

        if (RequestMethodInterface::METHOD_POST === $request->getMethod()) {
            $form->setData($request->getParsedBody());
            if ($form->isValid()) {
                $adapter = $this->authenticationService->getAdapter();
                $data = $form->getData();
                $adapter->setIdentity($data['identity']);
                $adapter->setCredential($data['password']);
                $authResult = $this->authenticationService->authenticate();
                if ($authResult->isValid()) {
                    $identity = $authResult->getIdentity();
                    $identity->setPassword(null);
                    $this->authenticationService->getStorage()->write($identity);

                    return new RedirectResponse($this->router->generateUri("page.home"));
                } else {
                    $this->messenger->addData('shouldRebind', true);
                    $this->forms->saveState($form);
                    $this->messenger->addError($authResult->getMessages(), 'user-login');
                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $this->messenger->addData('shouldRebind', true);
                $this->forms->saveState($form);
                $this->messenger->addError($this->forms->getMessages($form), 'user-login');
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->template->render('user::login', [
                'form' => $form
            ])
        );
    }
}
