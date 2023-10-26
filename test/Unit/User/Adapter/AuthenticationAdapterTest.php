<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\Adapter;

use Doctrine\ORM\EntityRepository;
use Frontend\App\Common\Message;
use Frontend\User\Adapter\AuthenticationAdapter;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserDetail;
use Frontend\User\Entity\UserRole;
use Frontend\User\Exception\AuthenticationAdapterException;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Adapter\Exception\ExceptionInterface;
use Laminas\Authentication\Result;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

use function array_merge;
use function password_hash;

use const PASSWORD_DEFAULT;

class AuthenticationAdapterTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testWillInstantiate(): void
    {
        $adapter = new AuthenticationAdapter($this->createMock(EntityRepository::class), []);

        $this->assertInstanceOf(AdapterInterface::class, $adapter);
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testValidateConfigInvalidIdentityClassProvided(): void
    {
        $this->expectException(AuthenticationAdapterException::class);
        $this->expectExceptionMessage(
            (AuthenticationAdapterException::invalidParam('identity_class'))->getMessage()
        );

        $adapter = new AuthenticationAdapter($this->createMock(EntityRepository::class), []);

        $result = $adapter->authenticate();
        $this->assertFalse($result->isValid());
        $this->assertNull($result->getIdentity());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testValidateConfigInvalidIdentityPropertyProvided(): void
    {
        $this->expectException(AuthenticationAdapterException::class);
        $this->expectExceptionMessage(
            (AuthenticationAdapterException::invalidParam('identity_property'))->getMessage()
        );

        $adapter = new AuthenticationAdapter($this->createMock(EntityRepository::class), [
            'identity_class' => User::class,
        ]);

        $result = $adapter->authenticate();
        $this->assertFalse($result->isValid());
        $this->assertNull($result->getIdentity());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testValidateConfigInvalidIdentityPropertyClassProvided(): void
    {
        $this->expectException(AuthenticationAdapterException::class);
        $this->expectExceptionMessage(
            (AuthenticationAdapterException::invalidParam('identity_class'))->getMessage()
        );

        $adapter = new AuthenticationAdapter($this->createMock(EntityRepository::class), [
            'identity_class' => 'invalid_class',
        ]);

        $result = $adapter->authenticate();
        $this->assertFalse($result->isValid());
        $this->assertNull($result->getIdentity());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testValidateConfigInvalidCredentialPropertyProvided(): void
    {
        $this->expectException(AuthenticationAdapterException::class);
        $this->expectExceptionMessage(
            (AuthenticationAdapterException::invalidParam('credential_property'))->getMessage()
        );

        $adapter = new AuthenticationAdapter($this->createMock(EntityRepository::class), [
            'identity_class'    => User::class,
            'identity_property' => 'identity',
        ]);

        $result = $adapter->authenticate();
        $this->assertFalse($result->isValid());
        $this->assertNull($result->getIdentity());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testValidateConfigNoIdentityProvided(): void
    {
        $this->expectException(AuthenticationAdapterException::class);
        $this->expectExceptionMessage(
            (AuthenticationAdapterException::noCredentialsProvided())->getMessage()
        );

        $adapter = new AuthenticationAdapter($this->createMock(EntityRepository::class), [
            'identity_class'      => User::class,
            'identity_property'   => 'identity',
            'credential_property' => 'password',
        ]);

        $result = $adapter
            ->setIdentity('')
            ->setCredential('password')
            ->authenticate();

        $this->assertFalse($result->isValid());
        $this->assertNull($result->getIdentity());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testValidateConfigNoCredentialProvided(): void
    {
        $this->expectException(AuthenticationAdapterException::class);
        $this->expectExceptionMessage(
            (AuthenticationAdapterException::noCredentialsProvided())->getMessage()
        );

        $adapter = new AuthenticationAdapter($this->createMock(EntityRepository::class), [
            'identity_class'      => User::class,
            'identity_property'   => 'identity',
            'credential_property' => 'password',
        ]);

        $result = $adapter
            ->setIdentity('test@dotkernel.com')
            ->setCredential('')
            ->authenticate();

        $this->assertFalse($result->isValid());
        $this->assertNull($result->getIdentity());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testCheckMethodThrowsException(): void
    {
        $class      = new class () {
        };
        $repository = $this->createMock(EntityRepository::class);

        $repository->expects($this->once())->method('findOneBy')->willReturn($class);

        $this->expectException(AuthenticationAdapterException::class);
        $this->expectExceptionMessage(
            (AuthenticationAdapterException::methodNotExists('getPassword', $class::class))->getMessage()
        );

        $adapter = new AuthenticationAdapter($repository, $this->getConfig($class));

        $result = $adapter
            ->setIdentity('test@dotkernel.com')
            ->setCredential('password')
            ->authenticate();

        $this->assertFalse($result->isValid());
        $this->assertNull($result->getIdentity());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testInvalidIdentityProvided(): void
    {
        $class      = new class () {
        };
        $config     = $this->getConfig($class);
        $repository = $this->createMock(EntityRepository::class);

        $repository->expects($this->once())->method('findOneBy')->willReturn(null);
        $adapter = new AuthenticationAdapter($repository, $config);

        $result = $adapter
            ->setIdentity('test@dotkernel.com')
            ->setCredential('password')
            ->authenticate();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertFalse($result->isValid());
        $this->assertSame(Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
        $this->assertIsArray($result->getMessages());
        $this->assertNotEmpty($result->getMessages());
        $this->assertSame($config['messages']['not_found'], $result->getMessages()[0]);
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testInvalidCredentialProvided(): void
    {
        $class      = new class () {
        };
        $config     = $this->getConfig($class);
        $repository = $this->createMock(EntityRepository::class);
        $user       = (new User())
            ->setIdentity('test@dotkernel.com')
            ->setPassword(password_hash('password', PASSWORD_DEFAULT));

        $repository->expects($this->once())->method('findOneBy')->willReturn($user);
        $adapter = new AuthenticationAdapter($repository, $config);

        $result = $adapter
            ->setIdentity('test@dotkernel.com')
            ->setCredential('invalid_password')
            ->authenticate();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertFalse($result->isValid());
        $this->assertSame(Result::FAILURE_CREDENTIAL_INVALID, $result->getCode());
        $this->assertIsArray($result->getMessages());
        $this->assertNotEmpty($result->getMessages());
        $this->assertSame($config['messages']['invalid_credential'], $result->getMessages()[0]);
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testExtraAuthenticationOptionsInvalidValueProvided(): void
    {
        $class = (new User())
            ->setIdentity('test@dotkernel.com')
            ->setPassword(password_hash('password', PASSWORD_DEFAULT));

        $repository = $this->createMock(EntityRepository::class);

        $repository->expects($this->once())->method('findOneBy')->willReturn($class);

        $this->expectException(AuthenticationAdapterException::class);
        $this->expectExceptionMessage(
            (AuthenticationAdapterException::invalidOptionValue('value', 'status'))->getMessage()
        );

        $adapter = new AuthenticationAdapter($repository, array_merge(
            $this->getConfig($class),
            [
                'options' => [
                    'status' => [
                        'values'  => '',
                        'message' => Message::USER_NOT_ACTIVATED,
                    ],
                ],
            ]
        ));

        $result = $adapter
            ->setIdentity('test@dotkernel.com')
            ->setCredential('password')
            ->authenticate();

        $this->assertFalse($result->isValid());
        $this->assertNull($result->getIdentity());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testExtraAuthenticationOptionsInvalidMessageProvided(): void
    {
        $class = (new User())
            ->setIdentity('test@dotkernel.com')
            ->setPassword(password_hash('password', PASSWORD_DEFAULT));

        $repository = $this->createMock(EntityRepository::class);

        $repository->expects($this->once())->method('findOneBy')->willReturn($class);

        $this->expectException(AuthenticationAdapterException::class);
        $this->expectExceptionMessage(
            (AuthenticationAdapterException::invalidOptionValue('message', 'status'))->getMessage()
        );

        $adapter = new AuthenticationAdapter($repository, array_merge(
            $this->getConfig($class),
            [
                'options' => [
                    'status' => [
                        'value'   => User::STATUS_ACTIVE,
                        'message' => '',
                    ],
                ],
            ]
        ));

        $result = $adapter
            ->setIdentity('test@dotkernel.com')
            ->setCredential('password')
            ->authenticate();

        $this->assertFalse($result->isValid());
        $this->assertNull($result->getIdentity());
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testExtraAuthenticationOptionsNonMatchingValueProvided(): void
    {
        $class = (new User())
            ->setIdentity('test@dotkernel.com')
            ->setPassword(password_hash('password', PASSWORD_DEFAULT))
            ->setStatus('invalid_status');

        $repository = $this->createMock(EntityRepository::class);

        $repository->expects($this->once())->method('findOneBy')->willReturn($class);

        $adapter = new AuthenticationAdapter($repository, array_merge(
            $this->getConfig($class),
            [
                'options' => [
                    'status' => [
                        'value'   => User::STATUS_ACTIVE,
                        'message' => 'test message',
                    ],
                ],
            ]
        ));

        $result = $adapter
            ->setIdentity('test@dotkernel.com')
            ->setCredential('password')
            ->authenticate();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertFalse($result->isValid());
        $this->assertSame(Result::FAILURE, $result->getCode());
        $this->assertIsArray($result->getMessages());
        $this->assertNotEmpty($result->getMessages());
        $this->assertSame('test message', $result->getMessages()[0]);
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testWillAuthenticate(): void
    {
        $class = (new User())
            ->setIdentity('test@dotkernel.com')
            ->setPassword(password_hash('password', PASSWORD_DEFAULT));

        $role   = (new UserRole())->setName(UserRole::ROLE_GUEST);
        $detail = (new UserDetail())
            ->setUser($class)
            ->setFirstName('Test')
            ->setLastName('DotKernel');

        $class
            ->addRole($role)
            ->setDetail($detail);

        $config     = $this->getConfig($class);
        $repository = $this->createMock(EntityRepository::class);

        $repository->expects($this->once())->method('findOneBy')->willReturn($class);

        $adapter = new AuthenticationAdapter($repository, $config);
        $result  = $adapter
            ->setIdentity('test@dotkernel.com')
            ->setCredential('password')
            ->authenticate();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isValid());
        $this->assertSame(Result::SUCCESS, $result->getCode());
        $this->assertIsArray($result->getMessages());
        $this->assertNotEmpty($result->getMessages());
        $this->assertSame($config['messages']['success'], $result->getMessages()[0]);
    }

    private function getConfig(object $class): array
    {
        return [
            'identity_class'      => $class::class,
            'identity_property'   => 'identity',
            'credential_property' => 'password',
            'messages'            => [
                'success'            => 'Authenticated successfully.',
                'not_found'          => 'Identity not found.',
                'invalid_credential' => 'Invalid credentials.',
            ],
        ];
    }
}
