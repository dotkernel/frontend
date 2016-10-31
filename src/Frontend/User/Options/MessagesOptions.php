<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Options;

use Zend\Stdlib\ArrayUtils;

/**
 * Class MessagesOptions
 * @package Dot\Frontend\User\Options
 */
class MessagesOptions extends \Dot\User\Options\MessagesOptions
{
    const MESSAGE_FIRST_NAME_EMPTY = 100;
    const MESSAGE_FIRST_NAME_CHARACTER_LIMIT = 101;
    const MESSAGE_LAST_NAME_EMPTY = 102;
    const MESSAGE_LAST_NAME_CHARACTER_LIMIT = 103;

    const MESSAGE_ACCOUNT_UPDATE_ERROR = 110;
    const MESSAGE_ACCOUNT_UPDATE_SUCCESS = 111;

    /**
     * MessagesOptions constructor.
     * @param array|null|\Traversable $options
     */
    public function __construct($options)
    {

        //merge the extended class's messages with the new default ones
        $this->messages = ArrayUtils::merge($this->messages, [
            MessagesOptions::MESSAGE_FIRST_NAME_EMPTY => 'First name is required and cannot be empty',
            MessagesOptions::MESSAGE_FIRST_NAME_CHARACTER_LIMIT => 'First name character limit exceeded',
            MessagesOptions::MESSAGE_LAST_NAME_EMPTY => 'Last name is required and cannot be empty',
            MessagesOptions::MESSAGE_LAST_NAME_CHARACTER_LIMIT => 'Last name character limit exceeded',

            MessagesOptions::MESSAGE_ACCOUNT_UPDATE_ERROR => 'Account update failed. Please try again',
            MessagesOptions::MESSAGE_ACCOUNT_UPDATE_SUCCESS => 'Account successfully updated',
        ]);
        parent::__construct($options);
    }
}