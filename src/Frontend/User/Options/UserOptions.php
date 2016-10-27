<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 10/25/2016
 * Time: 8:04 PM
 */

namespace Dot\Frontend\User\Options;

use Dot\User\Exception\InvalidArgumentException;

/**
 * Class UserOptions
 * @package Dot\Frontend\User\Options
 */
class UserOptions extends \Dot\User\Options\UserOptions
{
    /**
     * @param array|\Dot\User\Options\MessagesOptions $messagesOptions
     * @return $this
     */
    public function setMessagesOptions($messagesOptions)
    {
        if (is_array($messagesOptions)) {
            $this->messagesOptions = new MessagesOptions($messagesOptions);
        } elseif ($messagesOptions instanceof MessagesOptions) {
            $this->messagesOptions = $messagesOptions;
        } else {
            throw new InvalidArgumentException(sprintf(
                'MessagesOptions should be an array or an %s object. %s provided.',
                MessagesOptions::class,
                is_object($messagesOptions) ? get_class($messagesOptions) : gettype($messagesOptions)
            ));
        }
        return $this;
    }
}