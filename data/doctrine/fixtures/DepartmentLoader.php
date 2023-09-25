<?php

namespace Frontend\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Frontend\Contact\Entity\Department;

class DepartmentLoader implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $support = new Department();
        $support->setName('Support');

        $financial = new Department();
        $financial->setName('Financial');

        $hr = new Department();
        $hr->setName('Human Resource');

        $manager->persist($support);
        $manager->persist($financial);
        $manager->persist($hr);

        $manager->flush();
    }
}
