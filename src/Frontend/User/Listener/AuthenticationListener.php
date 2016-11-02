<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Listener;

use Dot\Authentication\Adapter\DbTable\DbCredentials;
use Dot\Authentication\Web\Action\LoginAction;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Frontend\User\Entity\UserEntity;
use Dot\Frontend\User\Mapper\UserDetailsMapperInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

/**
 * Class AuthenticationListener
 * @package Dot\Frontend\Authentication
 */
class AuthenticationListener extends AbstractListenerAggregate
{
    /** @var  UserDetailsMapperInterface */
    protected $userDetailsMapper;

    /**
     * AuthenticationListener constructor.
     * @param UserDetailsMapperInterface $userDetailsMapper
     */
    public function __construct(UserDetailsMapperInterface $userDetailsMapper)
    {
        $this->userDetailsMapper = $userDetailsMapper;
    }

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedEvents = $events->getSharedManager();

        //this will be called after the default prepare listener and the actual authentication call
        //use it for additional checks and data insertions into template
        $this->listeners[] = $sharedEvents->attach(
            LoginAction::class,
            AuthenticationEvent::EVENT_AUTHENTICATION_AUTHENTICATE,
            [$this, 'prepareAdapter'],
            10);

        $this->listeners[] = $sharedEvents->attach(
            LoginAction::class,
            AuthenticationEvent::EVENT_AUTHENTICATION_AUTHENTICATE,
            [$this, 'attachUserDetails'],
            -500);

        //more listeners if you want to customize the flow...
    }

    /**
     * Pre authentication listener that happens before the default authentication
     * Can be used to prepare some data for the form and pre-validate data
     *
     * @param AuthenticationEvent $e
     */
    public function prepareAdapter(AuthenticationEvent $e)
    {
        $request = $e->getRequest();
        $error = $e->getError();

        //we disable the login input labels here
        $e->setParam('showLabels', false);

        if ($request->getMethod() === 'POST' && empty($error)) {
            $identity = $e->getParam('identity', '');
            $credential = $e->getParam('password', '');
            if (empty($identity) || empty($credential)) {
                $e->setError('Credentials are required and cannot be empty');
                return;
            }

            $dbCredentials = new DbCredentials($identity, $credential);
            $e->setRequest($request->withAttribute(DbCredentials::class, $dbCredentials));
        }
    }

    /**
     * The authentication service fetches only the user entity from the user table
     * We initialize the details property too for the authenticated identity
     *
     * @param AuthenticationEvent $e
     */
    public function attachUserDetails(AuthenticationEvent $e)
    {
        if ($e->getRequest()->getMethod() === 'POST') {
            $authResult = $e->getAuthenticationResult();

            if ($authResult && $authResult->isValid() &&
                empty($e->getError())
            ) {
                /** @var UserEntity $identity */
                $identity = $e->getIdentity();
                if ($identity) {
                    $details = $this->userDetailsMapper->getUserDetails($identity->getId());
                    $identity->setDetails($details);
                }
            }
        }
    }

}