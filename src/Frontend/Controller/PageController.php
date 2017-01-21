<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\Controller;

use Dot\AnnotatedServices\Annotation\Service;
use Dot\Controller\AbstractActionController;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Class PageController
 * @package Dot\Frontend\Controller
 *
 * @Service
 */
class PageController extends AbstractActionController
{
    /**
     * @return RedirectResponse
     */
    public function indexAction()
    {
        return new RedirectResponse($this->url()->generate('home'));
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

    /**
     * @return HtmlResponse
     */
    public function premiumContentAction()
    {
        return new HtmlResponse($this->template()->render('page::premium-content'));
    }
}
