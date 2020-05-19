<?php


namespace Frontend\Contact\Controller;


use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Dot\Controller\AbstractActionController;
use Dot\FlashMessenger\FlashMessenger;
use Dot\Mail\Exception\MailException;
use Fig\Http\Message\RequestMethodInterface;
use Frontend\Contact\Form\ContactForm;
use Frontend\Contact\Service\MessageService;
use Frontend\Plugin\FormsPlugin;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Dot\AnnotatedServices\Annotation\Inject;


class ContactController extends AbstractActionController
{
    /** @var RouterInterface $router */
    protected RouterInterface $router;

    /** @var TemplateRendererInterface $template */
    protected TemplateRendererInterface $template;

    /** @var MessageService $messageService */
    protected MessageService $messageService;

    /** @var AuthenticationServiceInterface $authenticationService */
    protected AuthenticationServiceInterface $authenticationService;

    /** @var FlashMessenger $messenger */
    protected FlashMessenger $messenger;

    /** @var FormsPlugin $forms */
    protected FormsPlugin $forms;

    /**
     * UserController constructor.
     * @param MessageService $messageService
     * @param RouterInterface $router
     * @param TemplateRendererInterface $template
     * @param AuthenticationService $authenticationService
     * @param FlashMessenger $messenger
     * @param FormsPlugin $forms
     * @Inject({
     *     MessageService::class,
     *     RouterInterface::class,
     *     TemplateRendererInterface::class,
     *     AuthenticationService::class,
     *     FlashMessenger::class,
     *     FormsPlugin::class
     *     })
     */
    public function __construct(
        MessageService $messageService,
        RouterInterface $router,
        TemplateRendererInterface $template,
        AuthenticationService $authenticationService,
        FlashMessenger $messenger,
        FormsPlugin $forms
    ) {
        $this->messageService = $messageService;
        $this->router = $router;
        $this->template = $template;
        $this->authenticationService = $authenticationService;
        $this->messenger = $messenger;
        $this->forms = $forms;
    }

    /**
     * @return HtmlResponse
     */
    public function formAction(): ResponseInterface
    {
        $form = new ContactForm();
        return new HtmlResponse($this->template->render('contact::contact-form', [
            'form' => $form
        ]));
    }


    /**
     * @return ResponseInterface
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws MailException
     */
    public function saveContactMessageAction(): ResponseInterface
    {
        $form = new ContactForm();
        $request = $this->getRequest();

        $messages = [
            'type' => 'error',
            'text' => 'Something went wrong. Try again later.'
        ];

        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            $data = $request->getParsedBody();
            $data['subject'] = 'DotKernel Message ' . date("Y-m-d H:i:s");
            $form->setData($data);
            if ($form->isValid()) {
                $dataForm = $form->getData();

                $result = $this->messageService->processMessage($dataForm);

                $messages = [
                    'type' => 'error',
                    'text' => 'Error sending message. Try again or contact us at tech@dotkernel.com.'
                ];

                if ($result) {
                    $messages = [
                        'type' => 'success',
                        'text' => 'Thank you for contacting us!'
                    ];
                }
            } else {
                $messages = [
                    'type' => 'error',
                    'text' => $this->forms->getMessages($form)
                ];
            }
        }

        return new JsonResponse(['message' => $messages]);
    }
}