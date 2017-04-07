<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\User\Factory;

use Dot\Form\FormElementManager;
use Dot\User\Form\UserFieldset;
use Frontend\User\Fieldset\UserDetailsFieldset;
use Psr\Container\ContainerInterface;

/**
 * Class UserFieldsetDelegator
 * @package Frontend\User\Factory
 */
class UserFieldsetDelegator
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
