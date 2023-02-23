<?php

declare(strict_types=1);

namespace Frontend\Contact\Service;

use Doctrine\ORM\EntityRepository;
use Dot\Mail\Service\MailServiceInterface;
use Frontend\Contact\Entity\Message;
use Frontend\Contact\Repository\MessageRepository;
use Doctrine\ORM\EntityManager;
use Dot\AnnotatedServices\Annotation\Inject;
use Mezzio\Template\TemplateRendererInterface;

/**
 * Class MessageService
 * @package Frontend\Contact\Service
 */
final class MessageService implements MessageServiceInterface
{
    private readonly MessageRepository|EntityRepository $repository;
    private readonly MailServiceInterface $mailService;
    private readonly TemplateRendererInterface $templateRenderer;
    private array $config = [];
    /**
     * @var string
     */
    private const URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * MessageService constructor.
     *
     * @Inject({
     *     EntityManager::class,
     *     MailServiceInterface::class,
     *     TemplateRendererInterface::class,
     *     "config"
     * })
     */
    public function __construct(
        EntityManager $entityManager,
        MailServiceInterface $mailService,
        TemplateRendererInterface $templateRenderer,
        array $config = []
    ) {
        $this->repository = $entityManager->getRepository(Message::class);
        $this->mailService = $mailService;
        $this->templateRenderer = $templateRenderer;
        $this->config = $config;
    }

    public function getRepository(): MessageRepository
    {
        return $this->repository;
    }

    public function processMessage(array $data): bool
    {
        $message = new Message(
            $data['email'],
            $data['name'],
            $data['subject'],
            $data['message'],
            Message::PLATFORM_WEBSITE
        );

        $this->repository->saveMessage($message);

        return $this->sendContactMail($message);
    }

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
     */
    public function recaptchaIsValid($response): bool
    {
        $requestJson = [
            'response' => $response,
            'secret' => $this->config['recaptcha']['secretKey']
        ];

        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $requestJson);
        curl_setopt($curlHandle, CURLOPT_URL, self::URL);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);

        $checkRecaptchaResponse = curl_exec($curlHandle);
        $checkRecaptchaResponse = json_decode($checkRecaptchaResponse, true);
        return (bool) $checkRecaptchaResponse['success'];
    }
}
