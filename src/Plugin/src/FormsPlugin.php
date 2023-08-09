<?php

declare(strict_types=1);

namespace Frontend\Plugin;

use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Form\FormElementManager;
use Laminas\Form\Form;
use Laminas\Form\FormInterface;

class FormsPlugin implements PluginInterface
{
    protected FormElementManager $formElementManager;
    protected ?FlashMessengerInterface $flashMessenger;

    public function __construct(
        FormElementManager $formManager,
        FlashMessengerInterface $flashMessenger = null
    ) {
        $this->formElementManager = $formManager;
        $this->flashMessenger = $flashMessenger;
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

    protected function processFormMessages(array $formMessages): array
    {
        $messages = [];
        foreach ($formMessages as $message) {
            if (is_array($message)) {
                foreach ($message as $m) {
                    if (is_string($m)) {
                        $messages[] = $m;
                    } elseif (is_array($m)) {
                        $messages = array_merge($messages, $this->processFormMessages($m));
                    }
                }
            } elseif (is_string($message)) {
                $messages[] = $message;
            }
        }

        return $messages;
    }
}
