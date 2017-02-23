<?php
/**
 * @copyright: DotKernel
 * @library: dot-frontend
 * @author: n3vrax
 * Date: 2/23/2017
 * Time: 5:05 PM
 */

declare(strict_types = 1);

namespace App\User\Mapper;

use App\User\Entity\UserDetailsEntity;
use App\User\Entity\UserEntity;
use Dot\Ems\Event\MapperEvent;
use Zend\Hydrator\HydratorInterface;

/**
 * Class UserDbMapper
 * @package App\User\Mapper
 */
class UserDbMapper extends \Dot\User\Mapper\UserDbMapper
{
    /** @var  UserDetailsEntity */
    protected $userDetailsPrototype;

    /** @var  HydratorInterface */
    protected $userDetailsHydrator;

    /**
     * @param string $type
     * @param array $options
     * @return array
     */
    public function find(string $type = 'all', array $options = []): array
    {
        // append a join condition to the options
        // for user details every time we fetch users
        $options['joins'] += [
            'UserDetails' => [
                'on' => 'UserDetails.userId = User.id',
            ]
        ];

        return parent::find($type, $options);
    }

    public function onAfterLoad(MapperEvent $e)
    {
        parent::onAfterLoad($e);

        /** @var UserEntity $entity */
        $user = $e->getParam('entity');

        /** @var array $data */
        $data = $e->getParam('data');

        //load user details into user entity
        $details = $this->userDetailsHydrator->hydrate($data['UserDetails'], clone $this->userDetailsPrototype);
        $user->setDetails($details);
    }

    /**
     * @return UserDetailsEntity
     */
    public function getUserDetailsPrototype(): UserDetailsEntity
    {
        return $this->userDetailsPrototype;
    }

    /**
     * @param UserDetailsEntity $userDetailsPrototype
     */
    public function setUserDetailsPrototype(UserDetailsEntity $userDetailsPrototype)
    {
        $this->userDetailsPrototype = $userDetailsPrototype;
    }

    /**
     * @return HydratorInterface
     */
    public function getUserDetailsHydrator(): HydratorInterface
    {
        return $this->userDetailsHydrator;
    }

    /**
     * @param HydratorInterface $userDetailsHydrator
     */
    public function setUserDetailsHydrator(HydratorInterface $userDetailsHydrator)
    {
        $this->userDetailsHydrator = $userDetailsHydrator;
    }
}
