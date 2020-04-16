<?php

declare(strict_types=1);

namespace Frontend\User\Handler;

use Frontend\User\Service\UserService;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\FlashMessenger\FlashMessenger;
use Fig\Http\Message\RequestMethodInterface;
use Frontend\Plugin\FormsPlugin;
use Frontend\User\Form\RegisterForm;
use Laminas\Authentication\AuthenticationService;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class RegisterHandler
 * @package Frontend\User\Handler
 */
class RegisterHandler implements RequestHandlerInterface
{
    /** @var RouterInterface $router */
    protected $router;

    /** @var TemplateRendererInterface $template */
    protected $template;

    /** @var UserService $userService */
    protected $userService;

    /** @var AuthenticationService $authenticationService */
    protected $authenticationService;

    /** @var FlashMessenger $messenger */
    protected $messenger;

    /** @var FormsPlugin $forms */
    protected $forms;

    /**
     * RegisterHandler constructor.
     * @param RouterInterface $router
     * @param TemplateRendererInterface $template
     * @param UserService $userService
     * @param AuthenticationService $authenticationService
     * @param FlashMessenger $messenger
     * @param FormsPlugin $forms
     *
     * @Inject({RouterInterface::class, TemplateRendererInterface::class, UserService::class,
     *     AuthenticationService::class, FlashMessenger::class, FormsPlugin::class})
     */
    public function __construct(
        RouterInterface $router,
        TemplateRendererInterface $template,
        UserService $userService,
        AuthenticationService $authenticationService,
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
     * @throws \Dot\Mail\Exception\MailException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->authenticationService->hasIdentity()) {
            return new RedirectResponse($this->router->generateUri("page.home"));
        }

        $form = new RegisterForm();

        $shouldRebind = $this->messenger->getData('shouldRebind') ?? true;
        if ($shouldRebind) {
            $this->forms->restoreState($form);
        }

        if (RequestMethodInterface::METHOD_POST === $request->getMethod()) {
            $form->setData($request->getParsedBody());
            if ($form->isValid()) {
                $userData = $form->getData();
                try {
                    $user = $this->userService->createUser($userData);
                } catch (\Exception $e) {
                    $this->messenger->addData('shouldRebind', true);
                    $this->forms->saveState($form);
                    $this->messenger->addError($e->getMessage(), 'user-register');

                    return new RedirectResponse($request->getUri(), 303);
                }

                $this->userService->sendActivationMail($user);
                $this->messenger->addSuccess('Check the email to activate your account.', 'user-login');

                return new RedirectResponse($this->router->generateUri("user.login"));
            } else {
                $this->messenger->addData('shouldRebind', true);
                $this->forms->saveState($form);
                $this->messenger->addError($this->forms->getMessages($form), 'user-register');

                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->template->render('user::register', [
                'form' => $form
            ])
        );
    }
}
