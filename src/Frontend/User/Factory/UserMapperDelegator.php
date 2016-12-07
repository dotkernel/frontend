<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 11/24/2016
 * Time: 11:07 PM
 */

namespace Dot\Frontend\User\Factory;

use Dot\Ems\Mapper\DbMapper;
use Dot\Ems\Mapper\Relation\OneToOneRelation;
use Dot\Ems\Mapper\RelationalMapperInterface;
use Dot\Frontend\User\Entity\UserDetailsEntity;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Class UserDbMapperDelegator
 * @package Dot\Frontend\User\Factory
 */
class UserMapperDelegator implements DelegatorFactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        $mapper = $callback();
        if($mapper instanceof RelationalMapperInterface) {
            $detailsMapper = new DbMapper(
                'user_details',
                $container->get('database'),
                new UserDetailsEntity());
            $detailsMapper->setIdentifierName('userId');
            $relation = new OneToOneRelation($detailsMapper, 'userId', 'details');

            $mapper->addRelation($relation);
        }


        return $mapper;
    }
}