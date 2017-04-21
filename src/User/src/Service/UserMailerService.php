<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\User\Service;

use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Dot\Mail\Service\MailServiceInterface;
use Dot\User\Entity\ConfirmTokenEntity;
use Dot\User\Entity\ResetTokenEntity;
use Frontend\User\Entity\UserEntity;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Class UserMailerService
 * @package Frontend\User\Service
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
     * @return bool
     */
    public function sendActivationEmail(UserEntity $user, ConfirmTokenEntity $token): bool
    {
        $queryParams = ['email' => $user->getEmail(), 'token' => $token->getToken()];

        $confirmAccountUri = $this->urlHelper->generate(
            'user',
            ['action' => 'confirm-account'],
            $queryParams
        );

        $optOutUri = $this->urlHelper->generate(
            'user',
            ['action' => 'opt-out'],
            $queryParams
        );

        $message = $this->mailService->getMessage();
        $message->setTo(
            $user->getEmail(),
            $user->getDetails()->getLastName() . ' ' . $user->getDetails()->getFirstName()
        );
        $message->setSubject('DotKernel Account confirmation');
        // by using the mail service setBody method, you can change the mime type to html just by adding some html tags
        $this->mailService->setBody(sprintf(
            "Congratulations, %s %s, on registering with DotKernel!" .
            "<br><br>You are one step away to access your new account." .
            "<br>Just click the link below to confirm your account" .
            "<br><br><a href=\"%s\">Activate my DotKernel account</a>" .
            "<br><br>You will be redirected to the sign in page upon successful confirmation" .
            "<br><br>If you received this e-mail without you registering, " .
            "please click the link below, to un-register your e-mail address" .
            "<br><br><a href=\"%s\">Un-register my DotKernel account</a>",
            $user->getDetails()->getLastName(),
            $user->getDetails()->getFirstName(),
            $this->serverUrlHelper->generate($confirmAccountUri),
            $this->serverUrlHelper->generate($optOutUri)
        ));

        $result = $this->mailService->send();
        return $result->isValid();
    }

    /**
     * @param UserEntity $user
     * @param ResetTokenEntity $token
     * @return bool
     */
    public function sendPasswordRecoveryEmail(UserEntity $user, ResetTokenEntity $token): bool
    {
        $query = ['email' => $user->getEmail(), 'token' => $token->getToken()];
        $resetPasswordUri = $this->urlHelper->generate(
            'user',
            ['action' => 'reset-password'],
            $query
        );

        $message = $this->mailService->getMessage();
        $message->setTo(
            $user->getEmail(),
            $user->getDetails()->getLastName() . ' ' . $user->getDetails()->getFirstName()
        );
        $message->setSubject('DotKernel Password recovery');
        // by using the mail service setBody method, you can change the mime type to html just by adding some html tags
        $this->mailService->setBody(sprintf(
            "You have requested an account password reset" .
            "<br>If you didn't make this request, please ignore this e-mail" .
            "<br><br>In order to reset your password, click the link bellow" .
            "<br><br><a href=\"%s\">Reset my DotKernel account password</a>" .
            "<br><br>Please note this link will expire within an hour. Do not share this information with anyone!",
            $this->serverUrlHelper->generate($resetPasswordUri)
        ));

        $result = $this->mailService->send();
        return $result->isValid();
    }
}
