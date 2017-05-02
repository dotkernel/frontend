<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\App\Factory;

use Frontend\App\Form\ContactForm;
use Psr\Container\ContainerInterface;

/**
 * Class ContactFormFactory
 * @package Frontend\App\Factory
 */
class ContactFormFactory
{
    /**
     * @param ContainerInterface $container
     * @return ContactForm
     */
    public function __invoke(ContainerInterface $container)
    {
        $recaptchaOptions = $container->get('config')['recaptcha'];
        return new ContactForm($recaptchaOptions);
    }
}
