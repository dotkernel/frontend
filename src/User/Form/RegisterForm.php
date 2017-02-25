<?php
/**
 * @copyright: DotKernel
 * @library: dot-frontend
 * @author: n3vrax
 * Date: 2/23/2017
 * Time: 7:47 PM
 */

declare(strict_types = 1);

namespace App\User\Form;

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
