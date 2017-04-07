<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\User\Form;

/**
 * Class AccountForm
 * @package Frontend\User\Form
 */
class AccountForm extends \Dot\User\Form\AccountForm
{
    public function init()
    {
        parent::init();
        $this->setValidationGroup([
            'account_csrf',
            'user' => [
                'username',
                'details' => [
                    'firstName',
                    'lastName',
                    'phone',
                    'address'
                ]
            ],
            // add submit to validation group,
            // not needed usually bu needed for the form display helper partial template
            'submit'
        ]);
    }
}
