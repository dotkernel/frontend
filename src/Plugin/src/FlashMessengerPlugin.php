<?php

/**
 * @see https://github.com/dotkernel/dot-controller-plugin-flashmessenger/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-controller-plugin-flashmessenger/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\Plugin;

use Dot\FlashMessenger\FlashMessengerInterface;

/**
 * Class FlashMessengerPlugin
 * @package Frontend\Plugin\FlashMessenger
 */
class FlashMessengerPlugin implements PluginInterface
{
    /** @var FlashMessengerInterface */
    protected $flashMessenger;

    /**
     * FlashMessengerPlugin constructor.
     * @param FlashMessengerInterface $flashMessenger
     */
    public function __construct(FlashMessengerInterface $flashMessenger)
    {
        $this->flashMessenger = $flashMessenger;
    }

    /**
     * @param string $type
     * @param string $channel
     * @param mixed $value
     */
    public function addMessage(string $type, $value, string $channel = FlashMessengerInterface::DEFAULT_CHANNEL)
    {
        $this->flashMessenger->addMessage($type, $value, $channel);
    }

    /**
     * @param string $key
     * @param string $channel
     * @param mixed $value
     */
    public function addData(string $key, $value, string $channel = FlashMessengerInterface::DEFAULT_CHANNEL)
    {
        $this->flashMessenger->addData($key, $value, $channel);
    }

    /**
     * @param mixed $error
     * @param string $channel
     */
    public function addError($error, string $channel = FlashMessengerInterface::DEFAULT_CHANNEL)
    {
        $this->flashMessenger->addError($error, $channel);
    }

    /**
     * @param mixed $message
     * @param string $channel
     */
    public function addWarning($message, string $channel = FlashMessengerInterface::DEFAULT_CHANNEL)
    {
        $this->flashMessenger->addWarning($message, $channel);
    }

    /**
     * @param mixed $message
     * @param string $channel
     */
    public function addInfo($message, string $channel = FlashMessengerInterface::DEFAULT_CHANNEL)
    {
        $this->flashMessenger->addInfo($message, $channel);
    }

    /**
     * @param mixed $message
     * @param string $channel
     */
    public function addSuccess($message, string $channel = FlashMessengerInterface::DEFAULT_CHANNEL)
    {
        $this->flashMessenger->addSuccess($message, $channel);
    }

    /**
     * @param string $type
     * @param string $channel
     * @return array
     */
    public function getMessages(
        string $type = null,
        string $channel = FlashMessengerInterface::DEFAULT_CHANNEL
    ): array {
        return $this->flashMessenger->getMessages($type, $channel);
    }

    /**
     * @param string $key
     * @param string $channel
     * @return mixed
     */
    public function getData(string $key, string $channel = FlashMessengerInterface::DEFAULT_CHANNEL)
    {
        return $this->flashMessenger->getData($key, $channel);
    }
}
