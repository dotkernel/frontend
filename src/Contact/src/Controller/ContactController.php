<?php

namespace Frontend\Contact\Controller;

use Dot\AnnotatedServices\Annotation\Inject;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Dot\Controller\AbstractActionController;
use Dot\DebugBar\DebugBar;
use Dot\FlashMessenger\FlashMessenger;
use Dot\Mail\Exception\MailException;
use Fig\Http\Message\RequestMethodInterface;
use Frontend\App\Service\RecaptchaService;
use Frontend\Contact\Form\ContactForm;
use Frontend\Contact\Service\MessageService;
use Frontend\Plugin\FormsPlugin;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

class ContactController extends AbstractActionController
{
    /** @var RouterInterface $router */
    protected RouterInterface $router;

    /** @var TemplateRendererInterface $template */
    protected TemplateRendererInterface $template;

    /** @var MessageService $messageService */
    protected MessageService $messageService;

    /** @var RecaptchaService $recaptchaService */
    protected RecaptchaService $recaptchaService;

    /** @var AuthenticationServiceInterface $authenticationService */
    protected AuthenticationServiceInterface $authenticationService;

    /** @var FlashMessenger $messenger */
    protected FlashMessenger $messenger;

    /** @var FormsPlugin $forms */
    protected FormsPlugin $forms;

    /** @var DebugBar $debugBar */
    protected DebugBar $debugBar;

    /** @var array $config */
    protected $config;

    /**
     * ContactController constructor.
     * @param MessageService $messageService
     * @param RecaptchaService $recaptchaService
     * @param RouterInterface $router
     * @param TemplateRendererInterface $template
     * @param AuthenticationService $authenticationService
     * @param FlashMessenger $messenger
     * @param FormsPlugin $forms
     * @param DebugBar $debugBar
     * @param array $config
     * @Inject({
     *     MessageService::class,
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
        MessageService $messageService,
        RecaptchaService $recaptchaService,
        RouterInterface $router,
        TemplateRendererInterface $template,
        AuthenticationService $authenticationService,
        FlashMessenger $messenger,
        FormsPlugin $forms,
        DebugBar $debugBar,
        array $config = []
    ) {
        $this->messageService = $messageService;
        $this->recaptchaService = $recaptchaService;
        $this->router = $router;
        $this->template = $template;
        $this->authenticationService = $authenticationService;
        $this->messenger = $messenger;
        $this->forms = $forms;
        $this->debugBar = $debugBar;
        $this->config = $config;
    }

    /**
     * @return ResponseInterface
     * @throws MailException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function formAction(): ResponseInterface
    {
        $form = new ContactForm();
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

            $data['subject'] = 'DotKernel Message ' . date("Y-m-d H:i:s");
            $form->setData($data);
            if ($form->isValid()) {
                $dataForm = $form->getData();

                $result = $this->messageService->processMessage($dataForm);

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
            'form' => $form,
            'recaptchaSiteKey' => $this->config['recaptcha']['siteKey']
        ]));
    }
}
