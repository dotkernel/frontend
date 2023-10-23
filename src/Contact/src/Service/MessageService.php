<?php

declare(strict_types=1);

namespace Frontend\Contact\Service;

use Doctrine\ORM\EntityRepository;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\Mail\Exception\MailException;
use Dot\Mail\Service\MailServiceInterface;
use Frontend\Contact\Entity\Message;
use Frontend\Contact\Repository\MessageRepository;
use Mezzio\Template\TemplateRendererInterface;

class MessageService implements MessageServiceInterface
{
    /**
     * @Inject({
     *     MessageRepository::class,
     *     MailServiceInterface::class,
     *     TemplateRendererInterface::class,
     *     "config"
     * })
     */
    public function __construct(
        protected MessageRepository $repository,
        protected MailServiceInterface $mailService,
        protected TemplateRendererInterface $templateRenderer,
        protected array $config = []
    ) {
    }

    public function getRepository(): MessageRepository|EntityRepository
    {
        return $this->repository;
    }

    /**
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

    public function sendContactMail(Message $message): bool
    {
        $this->mailService->setBody(
            $this->templateRenderer->render('contact::email', [
                'config'  => $this->config,
                'message' => $message,
            ])
        );

        $this->mailService->setSubject($message->getSubject());
        $this->mailService->getMessage()->addFrom(
            $this->config['dot_mail']['default']['message_options']['from'],
            $this->config['dot_mail']['default']['message_options']['from_name']
        );
        $this->mailService->getMessage()->addTo(
            $this->config['contact']['message_receivers']['to'],
            'DotKernel Team'
        );
        $this->mailService->getMessage()->addCC(
            $this->config['contact']['message_receivers']['cc'],
            'DotKernel Team'
        );
        $this->mailService->getMessage()->setReplyTo($message->getEmail(), $message->getName());

        return $this->mailService->send()->isValid();
    }
}
