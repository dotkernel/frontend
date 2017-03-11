<?php
/**
 * @see https://github.com/dotkernel/dot-frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Frontend\User\Form;

use Zend\Stdlib\ArrayUtils;

/**
 * Class RegisterForm
 * @package App\User\Form
 */
class RegisterForm extends \Dot\User\Form\RegisterForm
{
    public function init()
    {
        parent::init();
        $validationGroup = $this->getValidationGroup();
        $validationGroup = ArrayUtils::merge($validationGroup, [
            'user' => [
                'details' => [
                    'firstName',
                    'lastName',
                ]
            ]
        ]);

        $this->setValidationGroup($validationGroup);
    }
}
