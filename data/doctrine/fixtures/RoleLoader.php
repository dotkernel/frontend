<?php

declare(strict_types=1);

namespace Frontend\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Frontend\User\Entity\UserRole;

class RoleLoader implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $adminRole = new UserRole();
        $adminRole->setName('admin');

        $userRole = new UserRole();
        $userRole->setName('user');

        $guestRole = new UserRole();
        $guestRole->setName('guest');

        $manager->persist($adminRole);
        $manager->persist($userRole);
        $manager->persist($guestRole);

        $manager->flush();
    }
}
