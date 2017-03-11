<?php
/**
 * @see https://github.com/dotkernel/dot-frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Frontend\User\Listener;

use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Dot\User\Entity\ConfirmTokenEntity;
use Dot\User\Entity\ResetTokenEntity;
use Dot\User\Event\TokenEvent;
use Dot\User\Event\TokenEventListenerInterface;
use Dot\User\Event\TokenEventListenerTrait;
use Dot\User\Event\UserEvent;
use Dot\User\Event\UserEventListenerInterface;
use Dot\User\Event\UserEventListenerTrait;
use Dot\User\Service\TokenService;
use Dot\User\Service\UserService;
use Frontend\User\Entity\UserEntity;
use Frontend\User\Service\UserMailerService;
use Zend\EventManager\EventManagerInterface;

/**
 * Class UserEventsListener
 * @package App\User\Listener
 *
 * @Service
 */
class UserEventsListener implements UserEventListenerInterface, TokenEventListenerInterface
{
    use UserEventListenerTrait,
        TokenEventListenerTrait {
        UserEventListenerTrait::attach as userEventAttach;
        UserEventListenerTrait::detach as userEventDetach;
        TokenEventListenerTrait::attach as tokenEventAttach;
        TokenEventListenerTrait::detach as tokenEventDetach;
    }

    /** @var  UserMailerService */
    protected $userMailer;

    /**
     * UserEventsListener constructor.
     * @param UserMailerService $userMailer
     *
     * @Inject({UserMailerService::class})
     */
    public function __construct(UserMailerService $userMailer)
    {
        $this->userMailer = $userMailer;
    }

    /**
     * @param UserEvent $e
     */
    public function onAfterRegistration(UserEvent $e)
    {
        // send an email if registration is with confirmation
        $token = $e->getParam('token');
        if ($token instanceof ConfirmTokenEntity) {
            /** @var UserEntity $user */
            $user = $e->getParam('user');
            $this->userMailer->sendActivationEmail($user, $token);
        }
    }

    /**
     * @param TokenEvent $e
     */
    public function onAfterSaveResetToken(TokenEvent $e)
    {
        $token = $e->getParam('token');
        if ($token instanceof ResetTokenEntity) {
            /** @var UserEntity $user */
            $user = $e->getParam('user');
            $this->userMailer->sendPasswordRecoveryEmail($user, $token);
        }
    }

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $identifiers = $events->getIdentifiers();
        if (in_array(UserService::class, $identifiers)) {
            $this->userEventAttach($events, $priority);
        }

        if (in_array(TokenService::class, $identifiers)) {
            $this->tokenEventAttach($events, $priority);
        }
    }

    /**
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        $identifiers = $events->getIdentifiers();
        if (in_array(UserService::class, $identifiers)) {
            $this->userEventDetach($events);
        }

        if (in_array(TokenService::class, $identifiers)) {
            $this->tokenEventDetach($events);
        }
    }
}
