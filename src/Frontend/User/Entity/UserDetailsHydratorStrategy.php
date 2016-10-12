<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/24/2016
 * Time: 7:18 PM
 */

namespace Dot\Frontend\User\Entity;

use Zend\Hydrator\HydratorInterface;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Class UserDetailsHydratorStrategy
 * @package Dot\Frontend\User\Entity
 */
class UserDetailsHydratorStrategy implements StrategyInterface
{
    /** @var HydratorInterface  */
    protected $userDetailsHydrator;

    /**
     * UserDetailsHydratorStrategy constructor.
     * @param HydratorInterface $hydrator
     */
    public function __construct(HydratorInterface $hydrator)
    {
        $this->userDetailsHydrator = $hydrator;
    }

    /**
     * @param mixed $details
     * @return array
     */
    public function extract($details)
    {
        if(!$details) {
            return [];
        }
        return $this->userDetailsHydrator->extract($details);
    }

    /**
     * @param mixed $value
     * @return UserDetailsEntity|mixed
     */
    public function hydrate($value)
    {
        if($value === null) {
            return $value;
        }

        $details = new UserDetailsEntity();
        $this->userDetailsHydrator->hydrate($value, $details);

        return $details;
    }

}