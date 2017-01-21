<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Controller;

use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Dot\Controller\AbstractActionController;
use Dot\Frontend\User\Service\UserServiceInterface;
use Dot\User\Form\UserFormManager;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * Class UserController
 * @package Dot\Frontend\User\Controller
 *
 * @Service
 */
class UserController extends AbstractActionController
{
    /** @var  UserFormManager */
    protected $formManager;

    /** @var  UserServiceInterface */
    protected $userService;

    /**
     * UserController constructor.
     * @param UserServiceInterface $userService
     * @param UserFormManager $formManager
     *
     * @Inject({"UserService", UserFormManager::class})
     */
    public function __construct(
        UserServiceInterface $userService,
        UserFormManager $formManager
    ) {
        $this->userService = $userService;
        $this->formManager = $formManager;
    }

    /**
     * @return HtmlResponse
     */
    public function changeEmailAction()
    {
        return new HtmlResponse($this->template()->render('user::change-email'));
    }

    /**
     * @return HtmlResponse
     */
    public function removeAccountAction()
    {
        return new HtmlResponse($this->template()->render('user::remove-account'));
    }

    /** helpers to add messages into the FlashMessenger */

    /**
     * @param array|string $messages
     */
    public function addError($messages)
    {
        $messages = (array)$messages;
        foreach ($messages as $message) {
            $this->flashMessenger()->addError($message);
        }
    }

    /**
     * @param array|string $messages
     */
    public function addInfo($messages)
    {
        $messages = (array)$messages;
        foreach ($messages as $message) {
            $this->flashMessenger()->addInfo($message);
        }
    }

    /**
     * @param array|string $messages
     */
    public function addWarning($messages)
    {
        $messages = (array)$messages;
        foreach ($messages as $message) {
            $this->flashMessenger()->addWarning($message);
        }
    }

    /**
     * @param array|string $messages
     */
    public function addSuccess($messages)
    {
        $messages = (array)$messages;
        foreach ($messages as $message) {
            $this->flashMessenger()->addSuccess($message);
        }
    }
}
