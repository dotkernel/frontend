<?php

declare(strict_types=1);

namespace Frontend\Contact\Service;

use Dot\DependencyInjection\Attribute\Inject;
use Dot\Mail\Service\MailServiceInterface;
use Frontend\Contact\Entity\Message;
use Frontend\Contact\Repository\MessageRepository;
use Frontend\Contact\Repository\MessageRepositoryInterface;
use Mezzio\Template\TemplateRendererInterface;

class MessageService implements MessageServiceInterface
{
    #[Inject(
        MessageRepositoryInterface::class,
        MailServiceInterface::class,
        TemplateRendererInterface::class,
        "config",
    )]
    public function __construct(
        protected MessageRepository $repository,
        protected MailServiceInterface $mailService,
        protected TemplateRendererInterface $templateRenderer,
        protected array $config = []
    ) {
    }

    public function getRepository(): MessageRepository
    {
        return $this->repository;
    }

    public function processMessage(array $data): bool
    {
        $subject = $this->config['contact']['subject'] ?? $this->config['dot_mail']['message_options']['subject']
            ?: $this->config['application']['name'] . ' Contact';

        $message = new Message(
            $data['email'],
            $data['name'],
            $subject,
            $data['message'],
            Message::PLATFORM_WEBSITE
        );

        $this->getRepository()->saveMessage($message);

        return $this->sendContactMail($message);
    }

    public function sendContactMail(Message $message): bool
    {
        $this->mailService->setBody(
            $this->templateRenderer->render('contact::email', [
                'config'  => $this->config,
                'message' => $message,
            ])
        );

        $this->mailService->setSubject($message->getSubject());

        $messageConfig     = $this->config['dot_mail']['default']['message_options'];
        $contactSender     = $this->config['contact']['message_sender'];
        $contactRecipients = $this->config['contact']['message_recipients'];

        $this->mailService->getMessage()->setFrom(
            $contactSender['from_email'] ?: $messageConfig['from'],
            $contactSender['from_name'] ?: $messageConfig['from_name']
        );

        $this->mailService->getMessage()->setTo(
            $contactRecipients['to'] ?: $messageConfig['to'],
            $contactRecipients['name'] ?: null
        );

        $this->mailService->getMessage()->setCc(
            $contactRecipients['cc'] ?: $messageConfig['cc'],
            $contactRecipients['name'] ?: null
        );

        $this->mailService->getMessage()->setBcc($contactRecipients['bcc'] ?: $messageConfig['bcc']);

        $this->mailService->getMessage()->setReplyTo($message->getEmail(), $message->getName());

        return $this->mailService->send()->isValid();
    }
}
