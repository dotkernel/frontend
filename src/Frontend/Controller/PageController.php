<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/10/2016
 * Time: 7:22 PM
 */

namespace Dot\Frontend\Controller;

use Dot\Controller\AbstractActionController;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Class PageController
 * @package Dot\Frontend\Controller
 */
class PageController extends AbstractActionController
{
    /**
     * @return RedirectResponse
     */
    public function indexAction()
    {
        return new RedirectResponse($this->urlHelper()->generate('home'));
    }

    /**
     * @return HtmlResponse
     */
    public function aboutUsAction()
    {
        return new HtmlResponse($this->template()->render('page::about-us'));
    }

    /**
     * @return HtmlResponse
     */
    public function whoWeAreAction()
    {
        return new HtmlResponse($this->template()->render('page::who-we-are'));
    }
}