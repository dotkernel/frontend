<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\App\Form;

use Zend\Form\Form;

/**
 * Class ContactForm
 * @package Frontend\App\Form
 */
class ContactForm extends Form
{
    /**
     * ContactForm constructor.
     * @param string $name
     * @param array $options
     */
    public function __construct($name = 'contactForm', array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
        // TODO: add elements
    }
}
