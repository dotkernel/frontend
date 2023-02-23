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
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ContactController
 * @package Frontend\Contact\Controller
 */
final class ContactController extends AbstractActionController
{
    private readonly TemplateRendererInterface $templateRenderer;
    private readonly MessageServiceInterface $messageService;
    private readonly RecaptchaService $recaptchaService;
    private readonly FlashMessenger $flashMessenger;
    private readonly FormsPlugin $formsPlugin;
    private readonly DebugBar $debugBar;
    private readonly array $config;

    /**
     * ContactController constructor.
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
        MessageServiceInterface $messageService,
        RecaptchaService $recaptchaService,
        RouterInterface $router,
        TemplateRendererInterface $templateRenderer,
        AuthenticationService $authenticationService,
        FlashMessenger $flashMessenger,
        FormsPlugin $formsPlugin,
        DebugBar $debugBar,
        array $config = []
    ) {
        $this->messageService = $messageService;
        $this->recaptchaService = $recaptchaService;
        $this->templateRenderer = $templateRenderer;
        $this->flashMessenger = $flashMessenger;
        $this->formsPlugin = $formsPlugin;
        $this->debugBar = $debugBar;
        $this->config = $config;
    }

    public function formAction(): ResponseInterface
    {
        $contactForm = new ContactForm();
        $serverRequest = $this->getRequest();

        if ($serverRequest->getMethod() === RequestMethodInterface::METHOD_POST) {
            $parsedBody = $serverRequest->getParsedBody();
            //check recaptcha
            if (isset($parsedBody['g-recaptcha-response'])) {
                if (! $this->recaptchaService->setResponse($parsedBody['g-recaptcha-response'])->isValid()) {
                    unset($parsedBody['g-recaptcha-response']);
                    $this->flashMessenger->addError('Captcha verification failed. Please try again.');
                    return new RedirectResponse($serverRequest->getUri(), 303);
                }
            } else {
                $this->flashMessenger->addError('Missing recaptcha');
                return new RedirectResponse($serverRequest->getUri(), 303);
            }

            $contactForm->get('subject')->setValue('DotKernel Message ' . date('Y-m-d H:i:s'));
            $contactForm->setData($parsedBody);
            if ($contactForm->isValid()) {
                $dataForm = $contactForm->getData();

                $result = $this->messageService->processMessage($dataForm);

                if ($result) {
                    $this->debugBar->stackData();
                    return new HtmlResponse($this->templateRenderer->render('contact::thank-you'));
                }
                $this->flashMessenger->addError('Something went wrong. Please try again later!');
                return new RedirectResponse($serverRequest->getUri(), 303);
            }
            $this->flashMessenger->addError($this->formsPlugin->getMessages($contactForm));
            return new RedirectResponse($serverRequest->getUri(), 303);
        }

        return new HtmlResponse($this->templateRenderer->render('contact::contact-form', [
            'form' => $contactForm,
            'recaptchaSiteKey' => $this->config['recaptcha']['siteKey']
        ]));
    }
}
