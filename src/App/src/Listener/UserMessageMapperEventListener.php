<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\App\Listener;

use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Dot\Mapper\Event\AbstractMapperEventListener;
use Dot\Mapper\Event\MapperEvent;
use Frontend\App\Service\NotificationMailService;

/**
 * Class UserMessageMapperEventListener
 * @package Frontend\App\Listener
 *
 * @Service
 */
class UserMessageMapperEventListener extends AbstractMapperEventListener
{
    /** @var  NotificationMailService */
    protected $notificationMailService;

    /**
     * UserMessageMapperEventListener constructor.
     * @param NotificationMailService $notificationMailService
     *
     * @Inject({NotificationMailService::class})
     */
    public function __construct(NotificationMailService $notificationMailService)
    {
        $this->notificationMailService = $notificationMailService;
    }

    /**
     * @param MapperEvent $e
     */
    public function onAfterSaveCommit(MapperEvent $e)
    {
        $message = $e->getParam('entity');
        $this->notificationMailService->sendUserMessageNotificationEmail($message);
    }
}
