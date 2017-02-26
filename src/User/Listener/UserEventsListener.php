<?php
/**
 * @copyright: DotKernel
 * @library: dot-frontend
 * @author: n3vrax
 * Date: 2/23/2017
 * Time: 8:03 PM
 */

declare(strict_types = 1);

namespace App\User\Listener;

use App\User\Entity\UserEntity;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Dot\Mail\Service\MailServiceInterface;
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
use Zend\EventManager\EventManagerInterface;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;

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

    /** @var  MailServiceInterface */
    protected $mailService;

    /** @var  ServerUrlHelper */
    protected $serverUrlHelper;

    /** @var  UrlHelper */
    protected $urlHelper;

    /**
     * UserEventsListener constructor.
     * @param MailServiceInterface $mailService
     * @param UrlHelper $urlHelper
     * @param ServerUrlHelper $serverUrlHelper
     *
     * @Inject({"dot-mail.service.default", UrlHelper::class, ServerUrlHelper::class})
     */
    public function __construct(
        MailServiceInterface $mailService,
        UrlHelper $urlHelper,
        ServerUrlHelper $serverUrlHelper
    ) {
        $this->mailService = $mailService;
        $this->urlHelper = $urlHelper;
        $this->serverUrlHelper = $serverUrlHelper;
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

            $confirmAccountUri = $this->urlHelper->generate('user', ['action' => 'confirm-account']);
            $queryParams = ['email' => $user->getEmail(), 'token' => $token->getToken()];
            $confirmAccountUri .= '?' . http_build_query($queryParams);

            $message = $this->mailService->getMessage();
            $message->setTo(
                $user->getEmail(),
                $user->getDetails()->getLastName() . ' ' . $user->getDetails()->getFirstName()
            );
            $message->setSubject('DotKernel Account confirmation');
            $message->setBody(sprintf(
                "Congratulations, %s %s, on registering with DotKernel!" .
                "\n\nYou are one step away to access your new account." .
                "\nJust click the link below to confirm your account" .
                "\n\n%s" .
                "\n\nYou will be redirected to the sign in page upon successful confirmation" .
                "\n\nIf you received this e-mail without you registering, please ignore this e-mail or contact us at" .
                "\n%s",
                $user->getDetails()->getLastName(),
                $user->getDetails()->getFirstName(),
                $this->serverUrlHelper->generate($confirmAccountUri),
                $this->serverUrlHelper->generate($this->urlHelper->generate('page', ['action' => 'contact']))
            ));

            $this->mailService->send();
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

            $resetPasswordUri = $this->urlHelper->generate('user', ['action' => 'reset-password']);
            $query = ['email' => $user->getEmail(), 'token' => $token->getToken()];
            $resetPasswordUri .= '?' . http_build_query($query);

            $message = $this->mailService->getMessage();
            $message->setTo(
                $user->getEmail(),
                $user->getDetails()->getLastName() . ' ' . $user->getDetails()->getFirstName()
            );
            $message->setSubject('DotKernel Password recovery');
            $message->setBody(sprintf(
                "You have requested an account password reset" .
                "\nIf you didn't make this request, please ignore this e-mail" .
                "\n\nIn order to reset your password, click the link bellow" .
                "\n\n%s" .
                "\n\nPlease note this link will expire within an hour. Do not share this information with anyone!",
                $this->serverUrlHelper->generate($resetPasswordUri)
            ));

            $this->mailService->send();
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
