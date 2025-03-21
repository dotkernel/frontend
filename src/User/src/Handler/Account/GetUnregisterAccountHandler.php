<?php

namespace Frontend\User\Handler\Account;

use Dot\DependencyInjection\Attribute\Inject;
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
use Exception;

class GetUnregisterAccountHandler implements RequestHandlerInterface
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
            return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
        }

        $user = $this->userService->findOneBy(['hash' => $hash]);
        if (! $user instanceof User) {
            $this->messenger->addError(Message::INVALID_ACTIVATION_CODE);
            return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
        }

        if (! $user->isPending()) {
            $this->messenger->addError(Message::USER_UNREGISTER_STATUS);
            return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
        }

        try {
            $this->userService->deleteUser($user);
        } catch (Exception $exception) {
            $this->messenger->addError($exception->getMessage());
            return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
        }

        $this->messenger->addSuccess(Message::USER_DEACTIVATED_SUCCESSFULLY);
        return new RedirectResponse($this->router->generateUri('user', ['action' => 'login']));
    }
}