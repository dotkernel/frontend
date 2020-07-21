<?php

namespace Frontend\User\Authentication;

use Doctrine\ORM\EntityManager;
use Frontend\User\Entity\User;
use Frontend\User\Repository\UserRepository;
use Laminas\Authentication\Adapter\AdapterInterface;
use Exception;
use Laminas\Authentication\Result;
use Doctrine\ORM\NonUniqueResultException;

class AuthenticationAdapter implements AdapterInterface
{
    /** @var string $identity */
    private $identity;

    /** @var string $credential */
    private $credential;

    /** @var EntityManager $entityManager */
    private $entityManager;

    /** @var array $config */
    private $config;

    /**
     * AuthenticationAdapter constructor.
     * @param $entityManager
     * @param array $config
     */
    public function __construct($entityManager, array $config)
    {
        $this->entityManager = $entityManager;
        $this->config = $config;
    }

    /**
     * @param string $identity
     * @return $this
     */
    public function setIdentity(string $identity): self
    {
        $this->identity = $identity;
        return $this;
    }

    /**
     * @param string $credential
     * @return $this
     */
    public function setCredential(string $credential): self
    {
        $this->credential = $credential;
        return $this;
    }

    /**
     * @return string
     */
    private function getIdentity(): string
    {
        return $this->identity;
    }

    /**
     * @return string
     */
    private function getCredential(): string
    {
        return $this->credential;
    }

    /**
     * @return Result
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function authenticate(): Result
    {
        $this->validateConfig();

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository($this->config['orm_default']['identity_class']);
        /** @var User $user */
        $user = $userRepository->findByIdentity(
            $this->config['orm_default']['identity_property'],
            $this->getIdentity()
        );
        if (! ($user instanceof User)) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                [$this->config['orm_default']['messages']['failure_not_found']]
            );
        }

        if (false === password_verify($this->getCredential(), $user->getPassword())) {
            return new Result(
                Result::FAILURE_CREDENTIAL_INVALID,
                null,
                [$this->config['orm_default']['messages']['failure_invalid_credential']]
            );
        }

        if ($user->getStatus() !== User::STATUS_ACTIVE) {
            return new Result(
                Result::FAILURE,
                null,
                [$this->config['orm_default']['messages']['failure_deactivated']]
            );
        }

        if ($user->isDeleted()) {
            return new Result(
                Result::FAILURE,
                null,
                [$this->config['orm_default']['messages']['failure_deleted']]
            );
        }

        $user->setPassword(null);
        $user->setHash(null);

        //TODO: Decide what to store in the session container
        // it could be a slim version of the logged user entity

        return new Result(
            Result::SUCCESS,
            $user,
            [$this->config['orm_default']['messages']['success']]
        );
    }

    /**
     * @throws Exception
     */
    private function validateConfig()
    {
        if (
            ! isset($this->config['orm_default']['identity_class']) ||
            ! class_exists($this->config['orm_default']['identity_class'])
        ) {
            throw new Exception("No or invalid param 'identity_class' provided.");
        }

        if (! isset($this->config['orm_default']['identity_property'])) {
            throw new Exception("No or invalid param 'identity_class' provided.");
        }

        if (! isset($this->config['orm_default']['credential_property'])) {
            throw new Exception("No or invalid param 'credential_property' provided.");
        }

        if (empty($this->identity) || empty($this->credential)) {
            throw new Exception('No credentials provided.');
        }
    }
}
