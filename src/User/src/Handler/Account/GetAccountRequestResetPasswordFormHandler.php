<?php

namespace Frontend\User\Handler\Account;

use Frontend\User\Form\RequestResetPasswordForm;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Dot\DependencyInjection\Attribute\Inject;

class GetAccountRequestResetPasswordFormHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
        TemplateRendererInterface::class,
        RequestResetPasswordForm::class,
    )]
    public function __construct(
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected RequestResetPasswordForm $form,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->form->setAttribute('action', $this->router->generateUri('account', ['action' => 'request-reset-password']));

        return new HtmlResponse(
            $this->template->render('user::request-reset-form', [
                'form' => $this->form,
            ])
        );
    }
}