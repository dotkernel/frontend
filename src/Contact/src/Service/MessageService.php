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
class MessageService implements MessageServiceInterface
{
    protected MessageRepository|EntityRepository $repository;
    protected MailServiceInterface $mailService;
    protected TemplateRendererInterface $templateRenderer;
    protected array $config = [];

    /**
     * MessageService constructor.
     * @param EntityManager $entityManager
     * @param MailServiceInterface $mailService
     * @param TemplateRendererInterface $templateRenderer
     * @param array $config
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

    public function getRepository(): MessageRepository|EntityRepository
    {
        return $this->repository;
    }

    /**
     * @param array $data
     * @return bool
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
}
