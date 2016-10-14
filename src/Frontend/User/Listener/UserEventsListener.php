<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/10/2016
 * Time: 4:48 PM
 */

namespace Dot\Frontend\User\Listener;

use Dot\Mail\Service\MailServiceInterface;
use Dot\User\Entity\UserEntityInterface;
use Dot\User\Event\ConfirmAccountEvent;
use Dot\User\Event\PasswordResetEvent;
use Dot\User\Event\RegisterEvent;
use Dot\User\Options\UserOptions;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Class UserEventsListener
 * @package Dot\Frontend\User\Listener
 */
class UserEventsListener extends AbstractListenerAggregate
{
    /** @var MailServiceInterface  */
    protected $mailService;

    /** @var  string */
    protected $confirmToken;

    /** @var  mixed */
    protected $resetToken;

    /** @var  ServerUrlHelper */
    protected $serverUrlHelper;

    /** @var  UrlHelper */
    protected $urlHelper;

    /** @var  UserOptions */
    protected $userOptions;

    /**
     * UserEventsListener constructor.
     * @param MailServiceInterface $mailService
     * @param ServerUrlHelper $serverUrlHelper
     * @param UrlHelper $urlHelper
     * @param UserOptions $userOptions
     */
    public function __construct(
        MailServiceInterface $mailService,
        ServerUrlHelper $serverUrlHelper,
        UrlHelper $urlHelper,
        UserOptions $userOptions
    )
    {
        $this->mailService = $mailService;
        $this->serverUrlHelper = $serverUrlHelper;
        $this->urlHelper = $urlHelper;
        $this->userOptions = $userOptions;
    }

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(RegisterEvent::EVENT_REGISTER_POST, [$this, 'onPostRegister'], $priority);

        $this->listeners[] = $events->attach(ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_TOKEN_POST,
            [$this, 'onConfirmTokenGenerated'], $priority);

        $this->listeners[] = $events->attach(PasswordResetEvent::EVENT_PASSWORD_RESET_TOKEN_POST,
            [$this, 'onResetPasswordTokenGenerated'], $priority);
    }

    /**
     * @param ConfirmAccountEvent $e
     */
    public function onConfirmTokenGenerated(ConfirmAccountEvent $e)
    {
        $data = $e->getData();
        $this->confirmToken = $data->token;
    }

    /**
     * @param PasswordResetEvent $e
     */
    public function onResetPasswordTokenGenerated(PasswordResetEvent $e)
    {
        $data = $e->getData();
        $this->resetToken = $data->token;

        if(!$this->resetToken) {
            return;
        }

        $user = $e->getUser();

        //send an email with the link to the reset page
        $resetPasswordUri = $this->urlHelper->generate('user', ['action' => 'reset-password']);
        $query = ['email' => $user->getEmail(), 'token' => $this->resetToken];
        $resetPasswordUri .= '?' . http_build_query($query);

        //sets the current request/response to make it available to mail events
        $this->mailService->setServerRequest($e->getRequest());
        $this->mailService->setResponse($e->getResponse());

        $message = $this->mailService->getMessage();
        $message->setTo($user->getEmail());
        $message->setSubject('DotKernel Password Reset');

        $message->setBody("You have requested an account password reset".
            "\nIf you didn't make this request, please ignore this email \n".
            "In order to reset your password, click the link bellow\n\n".
            $this->serverUrlHelper->generate($resetPasswordUri). "\n\n".
            "Please note this link will expired within an hour. Do not share this information with anyone!"
        );

        $this->mailService->send();

    }

    /**
     * @param RegisterEvent $e
     */
    public function onPostRegister(RegisterEvent $e)
    {
        //if we don't have a confirm token, just return
        if(!$this->confirmToken) {
            return;
        }
        
        /** @var UserEntityInterface $user */
        $user = $e->getUser();

        $confirmAccountUri = $this->urlHelper->generate('user', ['action' => 'confirm-account']);
        $query = ['email' => $user->getEmail(), 'token' => $this->confirmToken];
        $confirmAccountUri .= '?' . http_build_query($query);

        if($this->userOptions->getConfirmAccountOptions()->isEnableAccountConfirmation()) {

            $this->mailService->setServerRequest($e->getRequest());
            $this->mailService->setResponse($e->getResponse());

            $message = $this->mailService->getMessage();
            $message->setTo($user->getEmail());
            $message->setSubject('DotKernel Account confirmation');
            
            $message->setBody("Welcome to Dotkernel 3. Thank you for registering with us.".
                "\nClick the link below to confirm your account \n\n".
                $this->serverUrlHelper->generate($confirmAccountUri)
            );

            $this->mailService->send();
        }
    }
}