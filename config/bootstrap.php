<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/19/2016
 * Time: 2:01 PM
 */

/** @var \Zend\EventManager\EventManagerInterface $eventManager */
$eventManager = $container->get(\Zend\EventManager\EventManagerInterface::class);
/**
 * Register event listeners
 */
/** @var  $authenticationListeners */
$authenticationListeners = $container->get(\Dot\Frontend\Authentication\AuthenticationListener::class);
$authenticationListeners->attach($eventManager);

$eventManager->getSharedManager()->attach(
    \Dot\User\Form\RegisterForm::class,
    'init',
    new \Dot\Frontend\User\Listener\RegisterFormListener());
