<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
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
use Dot\User\Validator\NoRecordsExists;
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

    /**
     * UserController constructor.
     * @param UserServiceInterface $userService
     * @param UserFormManager $formManager
     */
    public function __construct(UserServiceInterface $userService, UserFormManager $formManager)
    {
        $this->userService = $userService;
        $this->formManager = $formManager;
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

        $userFormData = $this->flashMessenger()->getData('userFormData') ?: [];
        $userFormMessages = $this->flashMessenger()->getData('userFormMessages') ?: [];

        $form->setData($userFormData);
        $form->setMessages($userFormMessages);
        
        if($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            //in case username is changed we need to check its uniqueness
            //but only in case username was actually changed from the previous one
            if($data['username'] !== $identity->getUsername()) {
                /** @var AbstractValidator $usernameValidator */
                $usernameValidator = new NoRecordsExists([
                    'mapper' => $this->userService->getUserMapper(),
                    'key' => 'username',
                ]);
                $usernameValidator->setMessage('Username is already registered and cannot be used');

                //consider we want to change username
                $form->getInputFilter()->get('username')
                    ->getValidatorChain()
                    ->attach($usernameValidator);
            }
            $form->setData($data);

            $isValid = $form->isValid();

            $this->flashMessenger()->addData('userFormData', $data);
            $this->flashMessenger()->addData('userFormMessages', $form->getMessages());

            if($isValid) {
                /** @var UserEntityInterface $user */
                $user = $form->getData();

                /** @var UserOperationResult $result */
                $result = $this->userService->updateAccountInfo($user);

                if($result->isValid()) {
                    $this->addSuccess('Account successfully updated', $this->flashMessenger());
                    return new RedirectResponse($request->getUri());
                }
                else {
                    $this->addError($result->getMessages(), $this->flashMessenger());
                    return new RedirectResponse($request->getUri(), 303);
                }
            }
            else {
                $this->addError($this->getFormMessages($form->getMessages()), $this->flashMessenger());
                return new RedirectResponse($request->getUri(), 303);
            }
        }
        
        return new HtmlResponse($this->template()->render('app::account', ['form' => $form,]));
    }

    public function changeEmailAction()
    {
        return new HtmlResponse($this->template()->render('app::change-email'));
    }

    public function removeAccountAction()
    {
        return new HtmlResponse($this->template()->render('app::remove-account'));
    }

    /**
     * @param array $formMessages
     * @return array
     */
    protected function getFormMessages(array $formMessages)
    {
        $messages = [];
        foreach ($formMessages as $message) {
            if(is_array($message)) {
                foreach ($message as $m) {
                    if(is_string($m)) {
                        $messages[] = $m;
                    }
                    elseif(is_array($m)) {
                        $messages = array_merge($messages, $this->getFormMessages($message));
                        break;
                    }
                }
            }
        }

        return $messages;
    }
}