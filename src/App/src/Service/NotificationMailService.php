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
    protected $notificationList = ['n3vrax@gmail.com'];

    /**
     * NotificationMailService constructor.
     * @param MailService $mailService
     *
     * @Inject({"dot-mail.service.default"})
     */
    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
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

        $message->setSubject($userMessage->getSubject());
        $this->mailService->setBody(sprintf(
            "You have a new user message from %s" .
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
