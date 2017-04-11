<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\User\Authentication;

use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Dot\Authentication\Adapter\Db\DbCredentials;
use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Web\Event\AbstractAuthenticationEventListener;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Frontend\User\Entity\UserEntity;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Math\Rand;
use Zend\Session\Container;

/**
 * Class AuthenticationListener
 * @package Frontend\User\Authentication
 *
 * @Service
 */
class AuthenticationListener extends AbstractAuthenticationEventListener
{
    /** @var  UrlHelper */
    protected $urlHelper;

    /** @var  Container */
    protected $sessionContainer;

    /**
     * AuthenticationListener constructor.
     * @param UrlHelper $urlHelper
     * @param Container $sessionContainer
     *
     * @Inject({UrlHelper::class, "dot-session.user"})
     */
    public function __construct(
        UrlHelper $urlHelper,
        Container $sessionContainer
    ) {
        $this->urlHelper = $urlHelper;
        $this->sessionContainer = $sessionContainer;
    }

    /**
     * @param AuthenticationEvent $e
     */
    public function onBeforeAuthentication(AuthenticationEvent $e)
    {
        /** @var ServerRequestInterface $request */
        $request = $e->getParam('request');
        $error = $e->getParam('error');

        if (empty($error)) {
            /** @var array $data */
            $data = $e->getParam('data');
            $identity = $data['identity'] ?? '';
            $credential = $data['password'] ?? '';

            if (empty($identity) || empty($credential)) {
                $e->setParam('error', 'Credentials are required and cannot be empty');
                return;
            }

            $dbCredentials = new DbCredentials($identity, $credential);
            $e->setParam('request', $request->withAttribute(DbCredentials::class, $dbCredentials));
        }
    }

    /**
     * @param AuthenticationEvent $e
     * @return ResponseInterface
     */
    public function onAfterAuthentication(AuthenticationEvent $e)
    {
        /** @var AuthenticationInterface $authenticationService */
        $authenticationService = $e->getParam('authenticationService');
        $error = $e->getParam('error');
        if ($error) {
            /** @var UserEntity $user */
            $user = $e->getParam('user');
            if ($user && !$authenticationService->hasIdentity() && $user->getStatus() === UserEntity::STATUS_PENDING) {
                // go to a special page where user can resend their confirmation e-mail
                // we can return ResponseInterface here, as the authentication flow will take them into account
                $this->sessionContainer->salt = Rand::getString(32);
                $uri = $this->urlHelper->generate(
                    'user',
                    ['action' => 'pending-activation'],
                    [
                        'email' => $user->getEmail(),
                        'check' => sha1($user->getEmail() . $user->getPassword() . $this->sessionContainer->salt)
                    ]
                );
                return new RedirectResponse($uri);
            }
        }
        // just make IDE shut up about not returning a value
        return null;
    }
}
