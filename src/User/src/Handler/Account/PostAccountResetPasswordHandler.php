<?php

namespace Frontend\User\Handler\Account;

use Dot\FlashMessenger\FlashMessengerInterface;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Common\Message;
use Frontend\Plugin\FormsPlugin;
use Frontend\User\Entity\User;
use Frontend\User\Form\ResetPasswordForm;
use Frontend\User\Service\UserServiceInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Dot\DependencyInjection\Attribute\Inject;
use Exception;

class PostAccountResetPasswordHandler implements RequestHandlerInterface
{
    #[Inject(
        UserServiceInterface::class,
        RouterInterface::class,
        FlashMessengerInterface::class,
        FormsPlugin::class,
        ResetPasswordForm::class,
    )]
    public function __construct(
        protected UserServiceInterface $userService,
        protected RouterInterface $router,
        protected FlashMessengerInterface $messenger,
        protected FormsPlugin $forms,
        protected ResetPasswordForm $form,
    ) {
    }


    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->form->setData($request->getParsedBody());
        if (! $this->form->isValid()) {
            $this->messenger->addError($this->forms->getMessages($this->form));
            return new RedirectResponse($request->getUri(), StatusCodeInterface::STATUS_SEE_OTHER);
        }

        /** @var array $data */
        $data = $this->form->getData();
        $user = $this->userService->findOneBy(['identity' => $data['identity']]);
        if (! $user instanceof User) {
            $this->messenger->addInfo(Message::MAIL_SENT_RESET_PASSWORD);
            return new RedirectResponse($request->getUri());
        }

        try {
            $user = $this->userService->updateUser($user->createResetPassword());
        } catch (Exception $exception) {
            $this->messenger->addError($exception->getMessage());
            return new RedirectResponse($request->getUri(), StatusCodeInterface::STATUS_SEE_OTHER);
        }

        try {
            $this->userService->sendResetPasswordRequestedMail($user);
        } catch (Exception $exception) {
            $this->messenger->addError($exception->getMessage());
            return new RedirectResponse($request->getUri(), StatusCodeInterface::STATUS_SEE_OTHER);
        }

        $this->messenger->addInfo(Message::MAIL_SENT_RESET_PASSWORD);
        return new RedirectResponse($request->getUri());
    }
}