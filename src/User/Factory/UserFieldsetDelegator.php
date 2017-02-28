<?php
/**
 * @copyright: DotKernel
 * @library: dot-frontend
 * @author: n3vrax
 * Date: 2/23/2017
 * Time: 2:34 AM
 */

declare(strict_types = 1);

namespace Frontend\User\Factory;

use Dot\Form\FormElementManager;
use Dot\User\Form\UserFieldset;
use Frontend\User\Fieldset\UserDetailsFieldset;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Class UserFieldsetDelegator
 * @package App\User\Factory
 */
class UserFieldsetDelegator implements DelegatorFactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        /** @var FormElementManager $formManager */
        $formManager = $container->get('FormElementManager');

        /** @var UserFieldset $userFieldset */
        $userFieldset = $callback();
        /** @var UserDetailsFieldset $userDetailsFieldset */
        $userDetailsFieldset = $formManager->get('UserDetailsFieldset');

        $userFieldset->add($userDetailsFieldset, ['priority' => -10]);

        return $userFieldset;
    }
}
