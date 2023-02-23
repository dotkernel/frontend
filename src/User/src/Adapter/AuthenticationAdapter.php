<?php

declare(strict_types=1);

namespace Frontend\User\Adapter;

use Doctrine\ORM\EntityRepository;
use Exception;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserIdentity;
use Frontend\User\Entity\UserRole;
use Laminas\Authentication\Adapter\AbstractAdapter;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;

/**
 * Class AuthenticationAdapter
 * @package Frontend\User\Adapter
 */
final class AuthenticationAdapter extends AbstractAdapter implements AdapterInterface
{
    /**
     * @var string
     */
    private const METHOD_NOT_EXISTS = "Method %s not found in %s .";

    /**
     * @var string
     */
    private const OPTION_VALUE_NOT_PROVIDED = "Option '%s' not provided for '%s' option.";

    private readonly EntityRepository $entityRepository;
    private array $config;

    /**
     * AuthenticationAdapter constructor.
     */
    public function __construct(EntityRepository $entityRepository, array $config)
    {
        $this->entityRepository = $entityRepository;
        $this->config = $config;
    }

    /**
     * @throws Exception
     */
    public function authenticate(): Result
    {
        /** Check for the authentication configuration */
        $this->validateConfig();

        /** @var User $identityClass */
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

        $getCredential = 'get' . ucfirst((string) $this->config['credential_property']);

        /** Check if $getCredential method exist in the provided identity class */
        $this->checkMethod($identityClass, $getCredential);

        /** If passwords don't match, return failure response */
        if (!password_verify((string) $this->getCredential(), (string) $identityClass->$getCredential())) {
            return new Result(
                Result::FAILURE_CREDENTIAL_INVALID,
                null,
                [$this->config['messages']['invalid_credential']]
            );
        }

        /** Check for extra validation options */
        if (! empty($this->config['options'])) {
            foreach ($this->config['options'] as $property => $option) {
                $methodName = 'get' . ucfirst((string) $property);

                /** Check if $methodName exists in the provided identity class */
                $this->checkMethod($identityClass, $methodName);

                /** Check if value for the current option is provided */
                if (! array_key_exists('value', $option)) {
                    throw new Exception(sprintf(
                        self::OPTION_VALUE_NOT_PROVIDED,
                        'value',
                        $property
                    ));
                }

                /** Check if message for the current option is provided */
                if (!array_key_exists('message', $option)) {
                    throw new Exception(sprintf(
                        self::OPTION_VALUE_NOT_PROVIDED,
                        'message',
                        $property
                    ));
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
                $identityClass->getRoles()->map(static function (UserRole $userRole) : string {
                    return $userRole->getName();
                })->toArray(),
                $identityClass->getDetail()->getArrayCopy(),
            ),
            [$this->config['messages']['success']]
        );
    }

    /**
     * @throws Exception
     */
    private function validateConfig()
    {
        if (!isset($this->config['identity_class']) || !class_exists($this->config['identity_class'])) {
            throw new Exception("Missing or invalid param 'identity_class' provided.");
        }

        if (!isset($this->config['identity_property'])) {
            throw new Exception("Missing or invalid param 'identity_property' provided.");
        }

        if (!isset($this->config['credential_property'])) {
            throw new Exception("Missing or invalid param 'credential_property' provided.");
        }

        if (empty($this->identity) || empty($this->credential)) {
            throw new Exception('No credentials provided.');
        }
    }

    /**
     * @param $identityClass
     * @throws Exception
     */
    private function checkMethod($identityClass, string $methodName): void
    {
        if (!method_exists($identityClass, $methodName)) {
            throw new Exception(sprintf(
                self::METHOD_NOT_EXISTS,
                $methodName,
                get_class($identityClass)
            ));
        }
    }
}
