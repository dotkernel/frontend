<?php

declare(strict_types=1);

namespace FrontendTest\Unit\Plugin\Factory;

use Dot\FlashMessenger\FlashMessengerInterface;
use Frontend\Plugin\Factory\FormsPluginFactory;
use Frontend\Plugin\FormsPlugin;
use Laminas\Form\FormElementManager;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class FormsPluginFactoryTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testWillInstantiate(): void
    {
        $formElementManager = $this->createMock(FormElementManager::class);
        $flashMessenger = $this->createMock(FlashMessengerInterface::class);
        $container = $this->createMock(ContainerInterface::class);

        $container
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls($formElementManager, $flashMessenger);

        $object = (new FormsPluginFactory())($container);

        $this->assertInstanceOf(FormsPlugin::class, $object);
    }
}