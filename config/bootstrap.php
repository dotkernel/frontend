<?php

/**
 * This is a general place for things that needs to be run before application run
 * Use this mainly for event listener attachments, in case the module does not support listener attach through config
 */

/** @var \Zend\EventManager\EventManagerInterface $eventManager */
$eventManager = $container->get(\Zend\EventManager\EventManagerInterface::class);
/**
 * Register event listeners
 * This authentication listener prepares the request for authentication adapter
 * It also listen for post authentication, to enrich the identity with user details in case authentication succeeded
 */
/** @var  $authenticationListeners */
$authenticationListeners = $container->get(\Dot\Frontend\User\Listener\AuthenticationListener::class);
$authenticationListeners->attach($eventManager);

/**
 * This listener is for the register form
 * it injects the user details fieldset on form initialization
 */
$eventManager->getSharedManager()->attach(
    \Dot\User\Form\RegisterForm::class,
    'init',
    $container->get(\Dot\Frontend\User\Listener\RegisterFormListener::class)
);
