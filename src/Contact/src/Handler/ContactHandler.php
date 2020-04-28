<?php

declare(strict_types=1);

namespace Frontend\Contact\Handler;

use Frontend\Contact\Entity\Message;
use Frontend\Contact\Service\MessageServiceInterface;
use Dot\AnnotatedServices\Annotation\Inject;
use Frontend\User\Service\UserService;
use Dot\FlashMessenger\FlashMessenger;
use Fig\Http\Message\RequestMethodInterface;
use Frontend\App\Handler\AbstractHandler;
use Frontend\Contact\Form\ContactForm;
use Frontend\Plugin\FormsPlugin;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ContactHandler
 * @package Frontend\Contact\Handler
 */
class ContactHandler extends AbstractHandler
{
    /** @var RouterInterface $router */
    protected $router;

    /** @var TemplateRendererInterface $template */
    protected $template;

    /** @var UserService $userService */
    protected $userService;

    /** @var AuthenticationServiceInterface $authenticationService */
    protected $authenticationService;

    /** @var FlashMessenger $messenger */
    protected $messenger;

    /** @var FormsPlugin $forms */
    protected $forms;

    /** @var MessageServiceInterface $messageService */
    protected $messageService;

    /**
     * ContactHandler constructor.
     * @param RouterInterface $router
     * @param TemplateRendererInterface $template
     * @param UserService $userService
     * @param AuthenticationService $authenticationService
     * @param FlashMessenger $messenger
     * @param FormsPlugin $forms
     * @param MessageServiceInterface $messageService
     *
     * @Inject({RouterInterface::class, TemplateRendererInterface::class, AuthenticationService::class,
     *      UserService::class, FlashMessenger::class, FormsPlugin::class, MessageServiceInterface::class})
     */
    public function __construct(
        RouterInterface $router,
        TemplateRendererInterface $template,
        AuthenticationService $authenticationService,
        UserService $userService,
        FlashMessenger $messenger,
        FormsPlugin $forms,
        MessageServiceInterface $messageService
    ) {
        $this->router = $router;
        $this->template = $template;
        $this->userService = $userService;
        $this->authenticationService = $authenticationService;
        $this->messenger = $messenger;
        $this->forms = $forms;
        $this->messageService = $messageService;
    }

    /**
     * @return ResponseInterface
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function indexAction(): ResponseInterface
    {
        var_dump('index');
        exit;
    }

    /**
     * @return HtmlResponse
     */
    public function getContactFormAction()
    {
        $form = new ContactForm();
        return new HtmlResponse($this->template->render('contact-contact::contact-form', [
            'form' => $form
        ]));
    }

    /**
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveContactMessageAction()
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
