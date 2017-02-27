<?php
/**
 * @copyright: DotKernel
 * @library: dot-frontend
 * @author: n3vrax
 * Date: 2/23/2017
 * Time: 1:40 AM
 */

declare(strict_types = 1);

namespace App\User\Controller;

use App\User\Entity\UserEntity;
use App\User\Messages;
use App\User\Service\UserMailerService;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Dot\Controller\AbstractActionController;
use Dot\User\Options\UserOptions;
use Dot\User\Service\TokenServiceInterface;
use Dot\User\Service\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Dot\Controller\Plugin\Authentication\AuthenticationPlugin;
use Dot\Controller\Plugin\Authorization\AuthorizationPlugin;
use Dot\Controller\Plugin\FlashMessenger\FlashMessengerPlugin;
use Dot\Controller\Plugin\Forms\FormsPlugin;
use Dot\Controller\Plugin\TemplatePlugin;
use Dot\Controller\Plugin\UrlHelperPlugin;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Form\Form;
use Zend\Session\Container;

/**
 * Class UserController
 * @package App\User\Controller
 *
 * @method UrlHelperPlugin|UriInterface url(string $route = null, array $params = [])
 * @method FlashMessengerPlugin messenger()
 * @method FormsPlugin|Form forms(string $name = null)
 * @method TemplatePlugin|string template(string $template = null, array $params = [])
 * @method AuthenticationPlugin authentication()
 * @method AuthorizationPlugin isGranted(string $permission, array $roles = [], mixed $context = null)
 * @method Container session(string $namespace)
 *
 * @Service
 */
class UserController extends AbstractActionController
{
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
    public function pendingActivationAction(): ResponseInterface
    {
        $request = $this->getRequest();
        $params = $request->getQueryParams();

        $email = $params['email'] ?? '';
        $check = $params['check'] ?? '';
        $salt = $this->session('user')->salt ?? '';

        if (empty($email) || empty($check) || empty($salt)) {
            $this->messenger()->addError(Messages::INVALID_PARAMETERS);
            return new RedirectResponse($this->url('login'));
        }

        $user = $this->userService->findByEmail($email);
        if ($user && $user->getStatus() === UserEntity::STATUS_PENDING) {
            if ($check === sha1($user->getEmail() . $user->getPassword() . $salt)) {
                // show the page
                return new HtmlResponse($this->template(
                    'user::resend-activation',
                    [
                        'resendActivationUri' =>
                            $this->url('user', ['action' => 'resend-activation']) . '?' .
                                http_build_query(['email' => $email, 'check' => $check])
                    ]
                ));
            }
        }

        $this->messenger()->addError(Messages::INVALID_PARAMETERS);
        return new RedirectResponse($this->url('login'));
    }

    /**
     * @return ResponseInterface
     */
    public function resendActivationAction(): ResponseInterface
    {
        $request = $this->getRequest();
        $params = $request->getQueryParams();

        $email = $params['email'] ?? '';
        $check = $params['check'] ?? '';
        $salt = $this->session('user')->salt ?? '';

        if (empty($email) || empty($check) || empty($salt)) {
            $this->messenger()->addError(Messages::INVALID_PARAMETERS);
            return new RedirectResponse($this->url('login'));
        }

        /** @var UserEntity $user */
        $user = $this->userService->findByEmail($email);
        if ($user && $user->getStatus() === UserEntity::STATUS_PENDING) {
            if ($check === sha1($user->getEmail() . $user->getPassword() . $salt)) {
                $tokens = $this->tokenService->findConfirmTokens($user);
                if (!empty($tokens)) {
                    $confirmToken = $tokens[0];
                    $this->userMailer->sendActivationEmail($user, $confirmToken);

                    $session = $this->session('user');
                    unset($session->salt);

                    $this->messenger()->addSuccess(Messages::ACTIVATION_RESENT);
                    return new RedirectResponse($this->url('login'));
                }
            }
        }

        $this->messenger()->addError(Messages::INVALID_PARAMETERS);
        return new RedirectResponse($this->url('login'));
    }
}
