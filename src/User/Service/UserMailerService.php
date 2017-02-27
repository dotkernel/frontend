<?php
/**
 * @copyright: DotKernel
 * @library: dot-frontend
 * @author: n3vrax
 * Date: 2/26/2017
 * Time: 6:56 PM
 */

declare(strict_types = 1);

namespace App\User\Service;

use App\User\Entity\UserEntity;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Dot\Mail\Service\MailServiceInterface;
use Dot\User\Entity\ConfirmTokenEntity;
use Dot\User\Entity\ResetTokenEntity;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Class UserMailerService
 * @package App\User\Service
 *
 * @Service
 */
class UserMailerService
{
    /** @var  MailServiceInterface */
    protected $mailService;

    /** @var  UrlHelper */
    protected $urlHelper;

    /** @var  ServerUrlHelper */
    protected $serverUrlHelper;

    /**
     * UserMailerService constructor.
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
     * @param UserEntity $user
     * @param ConfirmTokenEntity $token
     * @return \Dot\Mail\Result\ResultInterface
     */
    public function sendActivationEmail(UserEntity $user, ConfirmTokenEntity $token)
    {
        $confirmAccountUri = $this->urlHelper->generate('user', ['action' => 'confirm-account']);
        $optOutUri = $this->urlHelper->generate('user', ['action' => 'opt-out']);

        $queryParams = http_build_query(['email' => $user->getEmail(), 'token' => $token->getToken()]);
        $confirmAccountUri .= '?' . $queryParams;
        $optOutUri .= '?' . $queryParams;

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
            "\n\nIf you received this e-mail without you registering, " .
            "please click the link below, to un-register your e-mail address" .
            "\n%s",
            $user->getDetails()->getLastName(),
            $user->getDetails()->getFirstName(),
            $this->serverUrlHelper->generate($confirmAccountUri),
            $this->serverUrlHelper->generate($optOutUri)
        ));

        return $this->mailService->send();
    }

    /**
     * @param UserEntity $user
     * @param ResetTokenEntity $token
     * @return \Dot\Mail\Result\ResultInterface
     */
    public function sendPasswordRecoveryEmail(UserEntity $user, ResetTokenEntity $token)
    {
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

        return $this->mailService->send();
    }
}
