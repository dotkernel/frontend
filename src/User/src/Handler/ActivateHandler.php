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
class ActivateHandler implements RequestHandlerInterface
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $hash = $request->getAttribute('hash', null);
        if (empty($hash)) {
            $this->messenger->addError(sprintf(Message::MISSING_PARAMETER, 'hash'), 'user-login');

            return new RedirectResponse($this->router->generateUri("user.login"));
        }

        $user = $this->userService->findOneBy(['hash' => $hash]);
        if (!($user instanceof User)) {
            $this->messenger->addError(Message::INVALID_ACTIVATION_CODE, 'user-login');

            return new RedirectResponse($this->router->generateUri("user.login"));
        }

        if ($user->getStatus() === User::STATUS_ACTIVE) {
            $this->messenger->addError(Message::USER_ALREADY_ACTIVATED, 'user-login');

            return new RedirectResponse($this->router->generateUri("user.login"));
        }

        try {
            $user = $this->userService->activateUser($user);
        } catch (\Exception $exception) {
            $this->messenger->addError($exception->getMessage(), 'user-login');

            return new RedirectResponse($this->router->generateUri("user.login"));
        }

        $this->messenger->addSuccess(Message::USER_ACTIVATED_SUCCESSFULLY, 'user-login');

        return new RedirectResponse($this->router->generateUri("user.login"));
    }
}
