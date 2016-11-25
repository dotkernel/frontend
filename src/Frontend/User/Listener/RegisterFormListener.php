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
        //TODO: remove some elements from the fieldset that you don't want in the register form
        //this is not the case right now, as this fieldset contains only lastName and firstName

        $baseFieldset->add($this->userDetailsFieldset);
        $form->getInputFilter()->get('user')->add($this->userDetailsFilter, 'details');
    }
}