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
    /**
     * @param UserEntityInterface $user
     * @return UserOperationResult
     */
    public function updateAccountInfo(UserEntityInterface $user)
    {
        $result = new UserOperationResult(true, 'Account successfully updated');

        try {
            $this->userMapper->beginTransaction();

            $this->getEventManager()->triggerEvent(
                $this->createUpdateEvent(UserUpdateEvent::EVENT_UPDATE_PRE, $user));

            $this->saveUser($user);

            $result->setUser($user);

            $this->getEventManager()->triggerEvent(
                $this->createUpdateEvent(UserUpdateEvent::EVENT_UPDATE_POST, $user));

            $this->userMapper->commit();
        } catch (\Exception $e) {
            error_log('Update user error: ' . $e->getMessage());
            $result = $this->createUserOperationResultWithException(
                $e, 'Account update failed. Please try again', $user);

            $this->getEventManager()->triggerEvent(
                $this->createUpdateEvent(UserUpdateEvent::EVENT_UPDATE_ERROR, $user, $result));

            $this->userMapper->rollback();
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