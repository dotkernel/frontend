<?php

namespace Frontend\Contact\Delegator;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Frontend\Contact\Entity\Department;
use Frontend\Contact\Form\ContactForm;
use Frontend\User\Entity\UserRole;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ContactFormDelegator implements DelegatorFactoryInterface
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws NotSupported
     */
    public function __invoke(
        ContainerInterface $container,
        $name,
        callable $callback,
        ?array $options = null
    ): ContactForm {

        /** @var ContactForm $contactForm */
        $contactForm = $callback();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);

        $departments = $entityManager->getRepository(Department::class)
            ->findAll();

        $options = [];

        /** @var Department $department */
        foreach ($departments as $department){
            $data = [
                'value' => $department->getUuid()->toString(),
                'label' => $department->getName(),
                'selected' => $department->getName() === 'Support' // here you can add your option which one to be default selected
            ];

            $options[] = $data;
        }

        $contactForm->setDepartment($options);

        return $contactForm;
    }
}
