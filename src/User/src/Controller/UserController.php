<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\User\Controller;

use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Dot\Controller\AbstractActionController;
use Dot\Controller\Plugin\Authentication\AuthenticationPlugin;
use Dot\Controller\Plugin\Authorization\AuthorizationPlugin;
use Dot\Controller\Plugin\FlashMessenger\FlashMessengerPlugin;
use Dot\Controller\Plugin\Forms\FormsPlugin;
use Dot\Controller\Plugin\TemplatePlugin;
use Dot\Controller\Plugin\UrlHelperPlugin;
use Dot\User\Options\UserOptions;
use Dot\User\Service\TokenServiceInterface;
use Dot\User\Service\UserServiceInterface;
use Frontend\User\Entity\UserEntity;
use Frontend\User\Messages;
use Frontend\User\Service\UserMailerService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Form\Form;
use Zend\Session\Container;

/**
 * Class UserController
 * @package Frontend\User\Controller
 *
 * @method UrlHelperPlugin|UriInterface url($r = null, $p = [], $q = [], $f = null, $o = [])
 * @method FlashMessengerPlugin messenger()
 * @method FormsPlugin|Form forms(string $name = null)
 * @method TemplatePlugin|string template(string $template = null, array $params = [])
 * @method AuthenticationPlugin authentication()
 * @method AuthorizationPlugin isGranted(string $permission, array $roles = [], $context = null)
 * @method Container session(string $namespace)
 *
 * @Service
 */
class UserController extends AbstractActionController
{
    const LOGIN_ROUTE = 'login';

    /** @var  UserOptions */
    protected $userOptions;

    /** @var  TokenServiceInterface */
    protected $tokenService;

    /** @var  UserServiceInterface */
    protected $userService;

    /** @var  UserMailerService */
    protected $userMailer;

    /**
     * UserController constructor.
     * @param UserOptions $userOptions
     * @param USerServiceInterface $userService
     * @param TokenServiceInterface $tokenService
     * @param UserMailerService $userMailer
     *
     * @Inject({UserServiceInterface::class, TokenServiceInterface::class,
     *      UserMailerService::class, UserOptions::class})
     */
    public function __construct(
        UserServiceInterface $userService,
        TokenServiceInterface $tokenService,
        UserMailerService $userMailer,
        UserOptions $userOptions
    ) {
        $this->userOptions = $userOptions;
        $this->userService = $userService;
        $this->tokenService = $tokenService;
        $this->userMailer = $userMailer;
    }

    /**
     * @return ResponseInterface
     */
    public function changeEmailAction(): ResponseInterface
    {
        // will be implemented in dot-user
        return new HtmlResponse($this->template(
            'user::change-email'
        ));
    }

    /**
     * @return ResponseInterface
     */
    public function removeAccountAction(): ResponseInterface
    {
        // will be implemented in dot-user
        return new HtmlResponse($this->template(
            'user::remove-account'
        ));
    }

    /**
     * @return ResponseInterface
     */
    public function pendingActivationAction(): ResponseInterface
    {
        if ($this->authentication()->hasIdentity()) {
            $this->messenger()->addWarning(Messages::SIGN_OUT_FIRST);
            return new RedirectResponse($this->url('user', ['action' => 'account']));
        }

        $request = $this->getRequest();
        $params = $request->getQueryParams();

        $email = $params['email'] ?? '';
        $check = $params['check'] ?? '';

        if (empty($email) || empty($check)) {
            $this->messenger()->addError(Messages::INVALID_PARAMETERS);
            return new RedirectResponse($this->url(static::LOGIN_ROUTE));
        }

        return new HtmlResponse($this->template(
            'user::resend-activation',
            [
                'resendActivationUri' =>
                    $this->url(
                        'user',
                        ['action' => 'resend-activation'],
                        ['email' => $email, 'check' => $check]
                    )
            ]
        ));
    }

    /**
     * @return ResponseInterface
     */
    public function resendActivationAction(): ResponseInterface
    {
        if ($this->authentication()->hasIdentity()) {
            $this->messenger()->addWarning(Messages::SIGN_OUT_FIRST);
            return new RedirectResponse($this->url('user', ['action' => 'account']));
        }

        $request = $this->getRequest();
        $params = $request->getQueryParams();

        $email = $params['email'] ?? '';
        $check = $params['check'] ?? '';
        $salt = $this->session('user')->salt ?? '';

        if (empty($email) || empty($check) || empty($salt)) {
            $this->messenger()->addError(Messages::INVALID_PARAMETERS);
            return new RedirectResponse($this->url(static::LOGIN_ROUTE));
        }

        /** @var UserEntity $user */
        $user = $this->userService->findByEmail($email);
        if ($user && $user->getStatus() === UserEntity::STATUS_PENDING) {
            if ($check === sha1($user->getEmail() . $user->getPassword() . $salt)) {
                $tokens = $this->tokenService->findConfirmTokens($user);
                if (empty($tokens)) {
                    // generate confirm token
                    $t = $this->tokenService->generateConfirmToken($user);
                    if (!$t->isValid()) {
                        $this->messenger()->addError(Messages::GENERATE_CONFIRM_TOKEN_ERROR);
                        return new RedirectResponse($this->url(static::LOGIN_ROUTE));
                    } else {
                        $tokens = [$t->getParam('token')];
                    }
                }

                if (!empty($tokens)) {
                    $confirmToken = $tokens[0];
                    $mailResult = $this->userMailer->sendActivationEmail($user, $confirmToken);

                    $session = $this->session('user');
                    unset($session->salt);

                    if (!$mailResult) {
                        $this->messenger()->addError(Messages::EMAIL_SEND_ERROR);
                    } else {
                        $this->messenger()->addSuccess(sprintf(Messages::ACTIVATION_RESENT, $email));
                    }

                    return new RedirectResponse($this->url(static::LOGIN_ROUTE));
                }
            }
        }

        $this->messenger()->addError(Messages::INVALID_PARAMETERS);
        return new RedirectResponse($this->url(static::LOGIN_ROUTE));
    }
}
