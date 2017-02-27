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
use App\User\Service\UserMailerService;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Dot\Controller\AbstractActionController;
use Dot\User\Options\MessagesOptions;
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
use Dot\Controller\Plugin\Session\SessionPlugin;
use Dot\Controller\Plugin\UrlHelperPlugin;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Form\Form;

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
 * @method SessionPlugin session()
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
     *
     * @Inject({UserServiceInterface::class, TokenServiceInterface::class, UserOptions::class})
     */
    public function __construct(
        UserServiceInterface $userService,
        TokenServiceInterface $tokenService,
        UserOptions $userOptions
    ) {
        $this->userOptions = $userOptions;
        $this->userService = $userService;
        $this->tokenService = $tokenService;
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

        if (empty($email) || empty($check)) {
            $this->messenger()->addError($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::ACCOUNT_UNCONFIRMED));
            return new RedirectResponse($this->url('login'));
        }

        $user = $this->userService->findByEmail($email);
        if ($user && $user->getStatus() === UserEntity::STATUS_PENDING) {
            if ($check === md5($user->getEmail() . $user->getPassword())) {
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

        $this->messenger()->addError($this->userOptions->getMessagesOptions()
            ->getMessage(MessagesOptions::ACCOUNT_UNCONFIRMED));
        return new RedirectResponse($this->url('login'));
    }

    public function resendActivationAction()
    {
        $request = $this->getRequest();
        $params = $request->getQueryParams();

        $email = $params['email'] ?? '';
        $check = $params['check'] ?? '';

        if (empty($email) || empty($check)) {
            $this->messenger()->addError($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::ACCOUNT_UNCONFIRMED));
            return new RedirectResponse($this->url('login'));
        }

        $user = $this->userService->findByEmail($email);
        if ($user && $user->getStatus() === UserEntity::STATUS_PENDING) {
            if ($check === md5($user->getEmail() . $user->getPassword())) {
                // TODO: send activation email
            }
        }
    }
}
