<?php

declare(strict_types=1);

namespace FrontendTest\Unit\Contact\Service;

use Dot\Mail\Exception\MailException;
use Dot\Mail\Result\ResultInterface;
use Dot\Mail\Service\MailServiceInterface;
use Frontend\Contact\Repository\MessageRepository;
use Frontend\Contact\Service\MessageService;
use Frontend\Contact\Service\MessageServiceInterface;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class MessageServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testWillInstantiate(): void
    {
        $service = new MessageService(
            $this->createMock(MessageRepository::class),
            $this->createMock(MailServiceInterface::class),
            $this->createMock(TemplateRendererInterface::class),
            [],
        );

        $this->assertInstanceOf(MessageServiceInterface::class, $service);
    }

    /**
     * @throws Exception
     * @throws MailException
     */
    public function testProcessMessage(): void
    {
        $messageRepository = $this->createMock(MessageRepository::class);
        $mailService       = $this->createMock(MailServiceInterface::class);
        $template          = $this->createMock(TemplateRendererInterface::class);
        $result            = $this->createMock(ResultInterface::class);

        $result->expects($this->once())->method('isValid')->willReturn(true);
        $mailService->expects($this->once())->method('send')->willReturn($result);

        $service = new MessageService(
            $messageRepository,
            $mailService,
            $template,
            [
                'contact'  => [
                    'message_receivers' => [
                        'to' => 'test@dotkernel.com',
                        'cc' => 'bcc@dotkernel.com',
                    ],
                ],
                'dot_mail' => [
                    'default' => [
                        'message_options' => [
                            'from'      => 'dotkernel@test.com',
                            'from_name' => 'DotKernel',
                        ],
                    ],
                ],
            ],
        );

        $this->assertTrue($service->processMessage([
            'email'   => 'test@dotkernel.com',
            'name'    => 'DotKernel',
            'subject' => 'test',
            'message' => 'test message',
        ]));
    }
}
