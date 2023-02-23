<?php

declare(strict_types=1);

namespace Frontend\Plugin;

use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Form\Factory\FormAbstractServiceFactory;
use Dot\Form\FormElementManager;
use Frontend\Plugin\Exception\RuntimeException;
use Laminas\Form\Form;
use Laminas\Form\FormInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class FormsPlugin
 * @package Frontend\Plugin
 */
final class FormsPlugin implements PluginInterface
{
    private readonly FormElementManager $formElementManager;
    private readonly ContainerInterface $container;
    private readonly ?FlashMessengerInterface $flashMessenger;

    /**
     * FormsPlugin constructor.
     * @param FlashMessengerInterface|null $flashMessenger
     */
    public function __construct(
        FormElementManager $formElementManager,
        ContainerInterface $container,
        FlashMessengerInterface $flashMessenger = null
    ) {
        $this->formElementManager = $formElementManager;
        $this->container = $container;
        $this->flashMessenger = $flashMessenger;
    }

    /**
     * @param string|null $name
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(string $name = null): mixed
    {
        if (is_null($name)) {
            return $this;
        }

        $result = null;
        // check the container first, in case there is a form to get through the abstract factory
        $abstractFormName = FormAbstractServiceFactory::PREFIX . '.' . $name;
        if ($this->container->has($abstractFormName)) {
            $result = $this->container->get($abstractFormName);
        } elseif ($this->formElementManager->has($name)) {
            $result = $this->formElementManager->get($name);
        }

        if (!$result) {
            throw new RuntimeException(
                "Form, fieldset or element with name {$result} could not be created. ' .
                'Are you sure you registered it in the form manager?"
            );
        }

        if ($result instanceof Form) {
            $this->restoreState($result);
        }

        return $result;
    }

    public function restoreState(Form $form): void
    {
        if ($this->flashMessenger instanceof FlashMessengerInterface) {
            $dataKey = $form->getName() . '_data';
            $messagesKey = $form->getName() . '_messages';

            $data = $this->flashMessenger->getData($dataKey) ?: [];
            $messages = $this->flashMessenger->getData($messagesKey) ?: [];

            $form->setData($data);
            $form->setMessages($messages);
        }
    }

    public function saveState(Form $form): void
    {
        if ($this->flashMessenger instanceof FlashMessengerInterface) {
            $dataKey = $form->getName() . '_data';
            $messagesKey = $form->getName() . '_messages';

            $this->flashMessenger->addData($dataKey, $form->getData(FormInterface::VALUES_AS_ARRAY));
            $this->flashMessenger->addData($messagesKey, $form->getMessages());
        }
    }

    public function getMessages(Form $form): array
    {
        return $this->processFormMessages(
            $form->getMessages()
        );
    }

    private function processFormMessages(array $formMessages): array
    {
        $messages = [];
        foreach ($formMessages as $formMessage) {
            if (is_array($formMessage)) {
                foreach ($formMessage as $m) {
                    if (is_string($m)) {
                        $messages[] = $m;
                    } elseif (is_array($m)) {
                        $messages = array_merge($messages, $this->processFormMessages($m));
                    }
                }
            } elseif (is_string($formMessage)) {
                $messages[] = $formMessage;
            }
        }

        return $messages;
    }

    public function getErrors(Form $form): array
    {
        return $this->processFormErrors(
            $form->getMessages()
        );
    }

    /**
     * @psalm-suppress InvalidArrayOffset
     */
    private function processFormErrors(array $formMessages): array
    {
        $errors = [];
        foreach ($formMessages as $key => $message) {
            if (is_array($message)) {
                if (!isset($errors[$key])) {
                    $errors[$key] = [];
                }

                foreach ($message as $k => $m) {
                    if (is_string($m)) {
                        $errors[$key][] = $m;
                    } elseif (is_array($m)) {
                        $errors[$key][$k] = $this->processFormErrors($m);
                    }
                }
            } elseif (is_string($message)) {
                $errors[] = $message;
            }
        }

        return $errors;
    }
}
