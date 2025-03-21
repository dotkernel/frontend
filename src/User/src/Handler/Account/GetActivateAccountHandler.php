<?php

namespace Frontend\User\Handler\Account;

use Dot\FlashMessenger\FlashMessengerInterface;
use Frontend\App\Common\Message;
use Frontend\User\Entity\User;
use Frontend\User\Service\UserServiceInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Dot\DependencyInjection\Attribute\Inject;
use Exception;

class GetActivateAccountHandler implements RequestHandlerInterface
{
    #[Inject(
        UserServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
    )]
    public function __construct(
        protected UserServiceInterface $userService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $hash = $request->getAttribute('hash', false);
        if (! $hash) {
            $this->messenger->addError(sprintf(Message::MISSING_PARAMETER, 'hash'));
            return new RedirectResponse($this->router->generateUri('user::login'));
        }

        $user = $this->userService->findOneBy(['hash' => $hash]);
        if (! $user instanceof User) {
            $this->messenger->addError(Message::INVALID_ACTIVATION_CODE);
            return new RedirectResponse($this->router->generateUri('user::login'));
        }

        if ($user->isActive()) {
            $this->messenger->addError(Message::USER_ALREADY_ACTIVATED);
            return new RedirectResponse($this->router->generateUri('user::login'));
        }

        try {
            $this->userService->activateUser($user);
        } catch (Exception $exception) {
            $this->messenger->addError($exception->getMessage());
            return new RedirectResponse($this->router->generateUri('user::login'));
        }

        $this->messenger->addSuccess(Message::USER_ACTIVATED_SUCCESSFULLY);
        return new RedirectResponse($this->router->generateUri('user::login'));
    }
}