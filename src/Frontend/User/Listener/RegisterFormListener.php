<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Listener;

use Zend\EventManager\Event;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * Class RegisterFormListener
 * @package Dot\Frontend\User\Listener
 */
class RegisterFormListener
{
    /** @var  Fieldset */
    protected $userDetailsFieldset;

    /** @var  InputFilter */
    protected $userDetailsFilter;

    /**
     * RegisterFormListener constructor.
     * @param Fieldset $userDetailsFieldset
     * @param InputFilter $userDetailsFilter
     */
    public function __construct(Fieldset $userDetailsFieldset, InputFilter $userDetailsFilter)
    {
        $this->userDetailsFieldset = $userDetailsFieldset;
        $this->userDetailsFilter = $userDetailsFilter;
    }

    /**
     * Listens for RegisterForm init event to add more elements to the original form
     *
     * @param Event $e
     */
    public function __invoke(Event $e)
    {
        /** @var Form $form */
        $form = $e->getTarget();
        $baseFieldset = $form->getBaseFieldset();

        $this->userDetailsFieldset->setName('details');

        //we remove phone and address from registration form, they are not required when registering
        $this->userDetailsFieldset->remove('phone')->remove('address');
        $this->userDetailsFilter->remove('phone')->remove('address');

        $baseFieldset->add($this->userDetailsFieldset);
        $form->getInputFilter()->get('user')->add($this->userDetailsFilter, 'details');
    }
}