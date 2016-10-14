<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/19/2016
 * Time: 10:54 PM
 */

namespace Dot\Frontend\Factory\Controller;

use Dot\Frontend\Controller\PageController;
use Interop\Container\ContainerInterface;

/**
 * Class PageControllerFactory
 * @package Dot\Frontend\Factory\Controller
 */
class PageControllerFactory
{
    /**
     * @param ContainerInterface $container
     * @return PageController
     */
    public function __invoke(ContainerInterface $container)
    {
        return new PageController();
    }
}