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
use Zend\Form\Form;
use Zend\Validator\AbstractValidator;

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

    /** @var  AbstractValidator */
    protected $usernameValidator;

    /**
     * UserController constructor.
     * @param UserServiceInterface $userService
     * @param UserFormManager $formManager
     * @param AbstractValidator|null $usernameValidator
     */
    public function __construct(
        UserServiceInterface $userService,
        UserFormManager $formManager,
        AbstractValidator $usernameValidator = null
    ) {
        $this->userService = $userService;
        $this->formManager = $formManager;
        $this->usernameValidator = $usernameValidator;
    }

    /**
     * @return HtmlResponse|RedirectResponse
     */
    public function accountAction()
    {
        $request = $this->getRequest();

        /** @var Form $form */
        $form = $this->formManager->get(UserForm::class);

        /** @var UserEntity $identity */
        $identity = $this->authentication()->getIdentity();
        $form->bind($identity);

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
            if (isset($data['username']) && $data['username'] !== $identity->getUsername() && $this->usernameValidator) {
                //consider we want to change username
                $form->getInputFilter()->get('user')->get('username')
                    ->getValidatorChain()
                    ->attach($this->usernameValidator);
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