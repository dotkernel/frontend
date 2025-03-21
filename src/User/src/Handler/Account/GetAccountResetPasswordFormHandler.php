<?php

namespace Frontend\User\Handler\Account;

use Frontend\User\Form\ResetPasswordForm;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Dot\DependencyInjection\Attribute\Inject;

class GetAccountResetPasswordFormHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
        TemplateRendererInterface::class,
    )]
    public function __construct(
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $form = new ResetPasswordForm();
        $hash = $request->getAttribute('hash') ?? null;

        $form->setAttribute('action', $this->router->generateUri(
            'account',
            [
                'action' => 'reset-password',
                'hash'   => $hash,
            ]
        ));

        return new HtmlResponse(
            $this->template->render('user::reset-password-form', [
                'form' => $form,
            ])
        );
    }
}