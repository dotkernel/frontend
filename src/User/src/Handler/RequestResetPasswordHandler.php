<?php

declare(strict_types=1);

namespace Frontend\User\Handler;

use Frontend\App\Common\Message;
use Frontend\User\Entity\User;
use Frontend\User\Service\UserService;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\FlashMessenger\FlashMessenger;
use Fig\Http\Message\RequestMethodInterface;
use Frontend\Plugin\FormsPlugin;
use Frontend\User\Form\RequestResetPasswordForm;
use Laminas\Authentication\AuthenticationService;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class ResetPasswordHandler
 * @package Frontend\User\Handler
 */
class RequestResetPasswordHandler implements RequestHandlerInterface
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
     * RequestResetPasswordHandler constructor.
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Dot\Mail\Exception\MailException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $form = new RequestResetPasswordForm();

        if (RequestMethodInterface::METHOD_POST === $request->getMethod()) {
            $form->setData($request->getParsedBody());
            if (!$form->isValid()) {
                $this->messenger->addError($this->forms->getMessages($form), 'request-reset');

                return new RedirectResponse($request->getUri(), 303);
            }

            $user = $this->userService->findOneBy(['identity' => $form->getData()['identity']]);
            if (!($user instanceof User)) {
                $this->messenger->addInfo(Message::MAIL_SENT_RESET_PASSWORD, 'request-reset');

                return new RedirectResponse($request->getUri());
            }

            try {
                $user = $this->userService->updateUser($user->createResetPassword());
            } catch (\Exception $exception) {
                $this->messenger->addError($exception->getMessage(), 'request-reset');

                return new RedirectResponse($request->getUri(), 303);
            }

            try {
                $this->userService->sendResetPasswordRequestedMail($user);
            } catch (\Exception $exception) {
                $this->messenger->addError($exception->getMessage(), 'request-reset');

                return new RedirectResponse($request->getUri(), 303);
            }

            $this->messenger->addInfo(Message::MAIL_SENT_RESET_PASSWORD, 'request-reset');

            return new RedirectResponse($request->getUri());
        }

        return new HtmlResponse(
            $this->template->render('user::request-reset-form', [
                'form' => $form
            ])
        );
    }
}
