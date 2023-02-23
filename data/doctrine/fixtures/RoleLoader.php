<?php

namespace Frontend\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Frontend\User\Entity\UserRole;

/**
 * Class RoleLoader
 * @package Frontend\Fixtures
 */
final class RoleLoader implements FixtureInterface
{
    public function load(ObjectManager $objectManager): void
    {
        $adminRole = new UserRole();
        $adminRole->setName('admin');

        $userRole = new UserRole();
        $userRole->setName('user');

        $guestRole = new UserRole();
        $guestRole->setName('guest');

        $objectManager->persist($adminRole);
        $objectManager->persist($userRole);
        $objectManager->persist($guestRole);

        $objectManager->flush();
    }
}

