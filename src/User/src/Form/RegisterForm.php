<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\User\Form;

/**
 * Class RegisterForm
 * @package Frontend\User\Form
 */
class RegisterForm extends \Dot\User\Form\RegisterForm
{
    public function init()
    {
        parent::init();
        // if using the form helper template, form element order will match the order in the validation group
        $validationGroup = [
            'register_csrf',
            'user' => [
                'username',
                'email',
                'details' => [
                    'firstName',
                    'lastName',
                ],
                'password',
                'passwordConfirm',
            ]
        ];
        if ($this->has('captcha')) {
            $validationGroup[] = 'captcha';
            $this->get('captcha')->setLabel('Click below to verify you are human');
        }
        // add submit to validation group,
        // not needed usually bu needed for the form display helper partial template
        $validationGroup[] = 'submit';

        $this->setValidationGroup($validationGroup);
    }
}
