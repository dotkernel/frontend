<?php

declare(strict_types=1);

namespace Frontend\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Frontend\User\Entity\UserRole;

/**
 * Class RoleLoader
 * @package Frontend\Fixtures
 */
class RoleLoader implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist((new UserRole())->setName('admin'));
        $manager->persist((new UserRole())->setName('user'));
        $manager->persist((new UserRole())->setName('guest'));

        $manager->flush();
    }
}
