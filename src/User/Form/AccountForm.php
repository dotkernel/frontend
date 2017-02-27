<?php
/**
 * @copyright: DotKernel
 * @library: dot-frontend
 * @author: n3vrax
 * Date: 2/23/2017
 * Time: 7:50 PM
 */

declare(strict_types = 1);

namespace App\User\Form;

use Zend\Stdlib\ArrayUtils;

/**
 * Class AccountForm
 * @package App\User\Form
 */
class AccountForm extends \Dot\User\Form\AccountForm
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
                    'phone',
                    'address'
                ]
            ]
        ]);

        $this->setValidationGroup($validationGroup);
    }
}
