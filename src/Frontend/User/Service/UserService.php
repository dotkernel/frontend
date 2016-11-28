<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Service;

use Dot\Frontend\User\Event\UserUpdateEvent;
use Dot\User\Entity\UserEntityInterface;
use Dot\User\Result\ResultInterface;
use Dot\User\Result\UserOperationResult;

/**
 * Class UserService
 * @package Dot\Frontend\User\Service
 */
class UserService extends \Dot\User\Service\UserService implements UserServiceInterface
{
    const MESSAGE_ACCOUNT_UPDATE_ERROR = 'Account could not be updated due to a server error. Please try again';
    const MESSAGE_ACCOUNT_UPDATE_OK = 'Your account was successfully updated';

    /**
     * @param UserEntityInterface $user
     * @return UserOperationResult
     */
    public function updateAccountInfo(UserEntityInterface $user)
    {
        $result = new UserOperationResult(true, static::MESSAGE_ACCOUNT_UPDATE_OK);

        $isAtomic = $this->isAtomicOperations();
        try {
            $this->setAtomicOperations(false);
            $this->mapper->beginTransaction();

            $this->getEventManager()->triggerEvent(
                $this->createUpdateEvent(UserUpdateEvent::EVENT_UPDATE_PRE, $user));

            $this->save($user);

            $result->setUser($user);

            $this->getEventManager()->triggerEvent(
                $this->createUpdateEvent(UserUpdateEvent::EVENT_UPDATE_POST, $user));

            $this->mapper->commit();
            $this->setAtomicOperations($isAtomic);

        } catch (\Exception $e) {
            error_log('Update user error: ' . $e->getMessage());

            $message = $this->debug ? $e->getMessage() : static::MESSAGE_ACCOUNT_UPDATE_ERROR;
            $result = $this->createUserOperationResultWithException(
                $e, $message, $user);

            $this->getEventManager()->triggerEvent(
                $this->createUpdateEvent(UserUpdateEvent::EVENT_UPDATE_ERROR, $user, $result));

            $this->mapper->rollback();
            $this->setAtomicOperations($isAtomic);
        }

        return $result;
    }

    protected function createUpdateEvent(
        $name = UserUpdateEvent::EVENT_UPDATE_PRE,
        UserEntityInterface $user = null,
        ResultInterface $result = null
    ) {
        $event = new UserUpdateEvent($this, $name, $user, $result);
        return $this->setupEventPsr7Messages($event);
    }
}