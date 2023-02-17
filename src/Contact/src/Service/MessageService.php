<?php

declare(strict_types=1);

namespace Frontend\Contact\Service;

use Frontend\Contact\Entity\Message;
use Frontend\Contact\Repository\MessageRepository;
use Doctrine\ORM\EntityManager;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\Mail\Exception\MailException;
use Dot\Mail\Service\MailService;
use Mezzio\Template\TemplateRendererInterface;

/**
 * Class MessageService
 * @package Frontend\Contact\Service
 */
class MessageService implements MessageServiceInterface
{
    protected MessageRepository $repository;
    protected MailService $mailService;
    protected TemplateRendererInterface $templateRenderer;
    protected array $config = [];

    /**
     * MessageService constructor.
     * @param EntityManager $entityManager
     * @param MailService $mailService
     * @param TemplateRendererInterface $templateRenderer
     * @param array $config
     *
     * @Inject({
     *     EntityManager::class,
     *     MailService::class,
     *     TemplateRendererInterface::class,
     *     "config"
     * })
     */
    public function __construct(
        EntityManager $entityManager,
        MailService $mailService,
        TemplateRendererInterface $templateRenderer,
        array $config = []
    ) {
        $this->repository = $entityManager->getRepository(Message::class);
        $this->mailService = $mailService;
        $this->templateRenderer = $templateRenderer;
        $this->config = $config;
    }

    /**
     * @return MessageRepository
     */
    public function getRepository(): MessageRepository
    {
        return $this->repository;
    }

    /**
     * @param array $data
     * @return bool
     * @throws MailException
     */
    public function processMessage(array $data): bool
    {
        $message = new Message(
            $data['email'],
            $data['name'],
            $data['subject'],
            $data['message'],
            Message::PLATFORM_WEBSITE
        );

        $this->getRepository()->saveMessage($message);

        return $this->sendContactMail($message);
    }

    /**
     * @param Message $message
     * @return bool
     * @throws MailException
     */
    public function sendContactMail(Message $message): bool
    {
        $this->mailService->setBody(
            $this->templateRenderer->render('contact::email', [
                'config' => $this->config,
                'message' => $message
            ])
        );

        $this->mailService->setSubject($message->getSubject());
        $this->mailService->getMessage()->addFrom(
            $this->config['dot_mail']['default']['message_options']['from'],
            $this->config['dot_mail']['default']['message_options']['from_name']
        );
        $this->mailService->getMessage()->addTo($this->config['contact']['message_receivers']['to'], 'DotKernel Team');
        $this->mailService->getMessage()->addCC($this->config['contact']['message_receivers']['cc'], 'DotKernel Team');
        $this->mailService->getMessage()->setReplyTo($message->getEmail(), $message->getName());

        return $this->mailService->send()->isValid();
    }

    /**
     * @param $response
     * @return bool
     */
    public function recaptchaIsValid($response): bool
    {
        $requestJson = [
            'response' => $response,
            'secret' => $this->config['recaptcha']['secretKey']
        ];
        $url = 'https://www.google.com/recaptcha/api/siteverify';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestJson);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $checkRecaptchaResponse = curl_exec($curl);
        $checkRecaptchaResponse = json_decode($checkRecaptchaResponse, true);
        if ($checkRecaptchaResponse['success'] == true) {
            return true;
        }
        return false;
    }
}
