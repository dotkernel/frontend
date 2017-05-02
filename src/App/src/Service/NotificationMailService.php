<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\App\Service;

use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Dot\Mail\Service\MailService;
use Frontend\App\Entity\UserMessageEntity;

/**
 * Class NotificationMailService
 * @package Frontend\App\Service
 *
 * @Service
 */
class NotificationMailService
{
    /** @var  MailService */
    protected $mailService;

    /** @var array  */
    protected $notificationList = [];

    /**
     * NotificationMailService constructor.
     * @param MailService $mailService
     * @param array $notificationReceivers
     *
     * @Inject({"dot-mail.service.default", "config.contact.notification_receivers"})
     */
    public function __construct(MailService $mailService, array $notificationReceivers = [])
    {
        $this->mailService = $mailService;
        $this->notificationList = $notificationReceivers;
    }

    /**
     * @param UserMessageEntity $userMessage
     * @return bool
     */
    public function sendUserMessageNotificationEmail(UserMessageEntity $userMessage)
    {
        if (empty($this->notificationList)) {
            return true;
        }

        $message = $this->mailService->getMessage();
        $message->setFrom($userMessage->getEmail(), $userMessage->getName());
        $message->setTo($this->notificationList);

        $message->setSubject("DotKernel notification");
        $this->mailService->setBody(sprintf(
            "A new user message from %s was submitted!" .
            "<br><br><strong>Subject:</strong>" .
            "<br>%s" .
            "<br><br><strong>Message:</strong>" .
            "<br>%s",
            $userMessage->getName(),
            $userMessage->getSubject(),
            nl2br($userMessage->getMessage())
        ));

        $result = $this->mailService->send();
        return $result->isValid();
    }
}
