<?php

declare(strict_types=1);

namespace Frontend\User\Adapter;

use Doctrine\ORM\EntityRepository;
use Frontend\User\Entity\UserIdentity;
use Frontend\User\Entity\UserRole;
use Frontend\User\Exception\AuthenticationAdapterException;
use Laminas\Authentication\Adapter\AbstractAdapter;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;

class AuthenticationAdapter extends AbstractAdapter implements AdapterInterface
{
    private EntityRepository $entityRepository;

    private array $config;

    public function __construct(EntityRepository $entityRepository, array $config)
    {
        $this->entityRepository = $entityRepository;
        $this->config = $config;
    }

    public function authenticate(): Result
    {
        $this->validateConfig();

        $identityClass = $this->entityRepository->findOneBy([
            $this->config['identity_property'] => $this->getIdentity()
        ]);

        if (null === $identityClass) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                [$this->config['messages']['not_found']]
            );
        }

        $getCredential = 'get' . ucfirst($this->config['credential_property']);

        $this->checkMethod($identityClass, $getCredential);

        if (false === password_verify($this->getCredential(), $identityClass->$getCredential())) {
            return new Result(
                Result::FAILURE_CREDENTIAL_INVALID,
                null,
                [$this->config['messages']['invalid_credential']]
            );
        }

        if (! empty($this->config['options'])) {
            foreach ($this->config['options'] as $property => $option) {
                $methodName = 'get' . ucfirst($property);

                $this->checkMethod($identityClass, $methodName);

                if (empty($option['value'])) {
                    throw AuthenticationAdapterException::invalidOptionValue('value', $property);
                }

                if (empty($option['message'])) {
                    throw AuthenticationAdapterException::invalidOptionValue('message', $property);
                }

                if ($identityClass->$methodName() !== $option['value']) {
                    return new Result(
                        Result::FAILURE,
                        null,
                        [$option['message']]
                    );
                }
            }
        }

        return new Result(
            Result::SUCCESS,
            new UserIdentity(
                $identityClass->getUuid()->toString(),
                $identityClass->getIdentity(),
                $identityClass->getRoles()->map(function (UserRole $userRole) {
                    return $userRole->getName();
                })->toArray(),
                $identityClass->getDetail()->getArrayCopy(),
            ),
            [$this->config['messages']['success']]
        );
    }

    private function validateConfig(): void
    {
        if (! isset($this->config['identity_class']) || ! class_exists($this->config['identity_class'])) {
            throw AuthenticationAdapterException::invalidParam('identity_class');
        }
        if (! isset($this->config['identity_property'])) {
            throw AuthenticationAdapterException::invalidParam('identity_property');
        }
        if (! isset($this->config['credential_property'])) {
            throw AuthenticationAdapterException::invalidParam('credential_property');
        }
        if (empty($this->identity) || empty($this->credential)) {
            throw AuthenticationAdapterException::noCredentialsProvided();
        }
    }

    private function checkMethod(object $identityClass, string $methodName): void
    {
        if (! method_exists($identityClass, $methodName)) {
            throw AuthenticationAdapterException::methodNotExists($methodName, get_class($identityClass));
        }
    }
}
