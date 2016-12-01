<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Controller;

use Dot\Controller\AbstractActionController;
use Dot\Frontend\User\Entity\UserEntity;
use Dot\Frontend\User\Form\UserForm;
use Dot\Frontend\User\Service\UserServiceInterface;
use Dot\User\Entity\UserEntityInterface;
use Dot\User\Form\UserFormManager;
use Dot\User\Result\UserOperationResult;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Class UserController
 * @package Dot\Frontend\User\Controller
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
     */
    public function __construct(
        UserServiceInterface $userService,
        UserFormManager $formManager
    ) {
        $this->userService = $userService;
        $this->formManager = $formManager;
    }

    /**
     * @return HtmlResponse|RedirectResponse
     * @throws \Exception
     */
    public function accountAction()
    {
        $request = $this->getRequest();

        /** @var UserForm $form */
        $form = $this->formManager->get(UserForm::class);

        /** @var UserEntity $identity */
        $identity = $this->authentication()->getIdentity();
        $user = $this->userService->find([$this->userService->getMapper()->getIdentifierName() => $identity->getId()]);
        //this should never happen, that's why we treat it as exception
        if(!$user instanceof UserEntityInterface) {
            throw new \Exception('Could not load user entity for identity ID');
        }
        //var_dump($user);exit;
        $form->bind($user);

        /**
         * Get previous form data stored in session, to re-display the information and/or errors
         */
        $userFormData = $this->flashMessenger()->getData('userFormData') ?: [];
        $userFormMessages = $this->flashMessenger()->getData('userFormMessages') ?: [];

        $form->setData($userFormData);
        $form->setMessages($userFormMessages);

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            //in case username is changed we need to check its uniqueness
            //but only in case username was actually changed from the previous one
            if (isset($data['user']['username']) && $data['user']['username'] === $identity->getUsername()) {
                //consider we don't want to change username, remove the uniqueness check
                $form->removeUsernameValidation();
                $form->applyValidationGroup();
            }

            $form->setData($data);

            $isValid = $form->isValid();

            //add form data and messages to the session, in case we do a PRG redirect
            $this->flashMessenger()->addData('userFormData', $data);
            $this->flashMessenger()->addData('userFormMessages', $form->getMessages());

            if ($isValid) {
                /** @var UserEntityInterface $user */
                $user = $form->getData();

                /** @var UserOperationResult $result */
                $result = $this->userService->updateAccountInfo($user);

                if ($result->isValid()) {
                    $this->addSuccess('Your account was successfully updated');
                    return new RedirectResponse($request->getUri());
                } else {
                    $this->addError($result->getMessages());
                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $this->addError($this->getFormMessages($form->getMessages()));
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse($this->template()->render('user::account', ['form' => $form,]));
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

    /**
     * @param array $formMessages
     * @return array
     */
    protected function getFormMessages(array $formMessages)
    {
        $messages = [];
        foreach ($formMessages as $message) {
            if (is_array($message)) {
                foreach ($message as $m) {
                    if (is_string($m)) {
                        $messages[] = $m;
                    } elseif (is_array($m)) {
                        $messages = array_merge($messages, $this->getFormMessages($message));
                        break;
                    }
                }
            }
        }

        return $messages;
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