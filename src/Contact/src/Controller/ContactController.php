<?php

declare(strict_types=1);

namespace Frontend\Contact\Controller;

use Dot\AnnotatedServices\Annotation\Inject;
use Dot\Controller\AbstractActionController;
use Dot\DebugBar\DebugBar;
use Dot\FlashMessenger\FlashMessenger;
use Fig\Http\Message\RequestMethodInterface;
use Frontend\App\Service\RecaptchaService;
use Frontend\Contact\Form\ContactForm;
use Frontend\Contact\Service\MessageServiceInterface;
use Frontend\Plugin\FormsPlugin;
use Laminas\Authentication\AuthenticationService;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

class ContactController extends AbstractActionController
{
    /**
     * @Inject({
     *     MessageServiceInterface::class,
     *     RecaptchaService::class,
     *     RouterInterface::class,
     *     TemplateRendererInterface::class,
     *     AuthenticationService::class,
     *     FlashMessenger::class,
     *     FormsPlugin::class,
     *     DebugBar::class,
     *     "config"
     *     })
     */
    public function __construct(
        protected MessageServiceInterface $messageService,
        protected RecaptchaService $recaptchaService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected AuthenticationService $authenticationService,
        protected FlashMessenger $messenger,
        protected FormsPlugin $forms,
        protected DebugBar $debugBar,
        protected array $config = []
    ) {
    }

    public function formAction(): ResponseInterface
    {
        $form    = new ContactForm();
        $request = $this->getRequest();

        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            $data = $request->getParsedBody();
            //check recaptcha
            if (isset($data['g-recaptcha-response'])) {
                if (! $this->recaptchaService->setResponse($data['g-recaptcha-response'])->isValid()) {
                    unset($data['g-recaptcha-response']);
                    $this->messenger->addError('Captcha verification failed. Please try again.');
                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $this->messenger->addError('Missing recaptcha');
                return new RedirectResponse($request->getUri(), 303);
            }
            $data['subject'] = $data['subject'] ?: $this->config['application']['name'] . ' Contact';
            $form->setData($data);
            if ($form->isValid()) {
                /** @var array $dataForm */
                $dataForm = $form->getData();
                $result   = $this->messageService->processMessage($dataForm);

                if ($result) {
                    $this->debugBar->stackData();
                    return new HtmlResponse($this->template->render('contact::thank-you'));
                } else {
                    $this->messenger->addError('Something went wrong. Please try again later!');
                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $this->messenger->addError($this->forms->getMessages($form));
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse($this->template->render('contact::contact-form', [
            'form'             => $form,
            'recaptchaSiteKey' => $this->config['recaptcha']['siteKey'],
        ]));
    }
}
