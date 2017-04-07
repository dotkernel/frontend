<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

namespace Frontend\App\Controller;

use Dot\AnnotatedServices\Annotation\Service;
use Dot\Controller\AbstractActionController;
use Dot\Controller\Plugin\Authentication\AuthenticationPlugin;
use Dot\Controller\Plugin\Authorization\AuthorizationPlugin;
use Dot\Controller\Plugin\FlashMessenger\FlashMessengerPlugin;
use Dot\Controller\Plugin\Forms\FormsPlugin;
use Dot\Controller\Plugin\TemplatePlugin;
use Dot\Controller\Plugin\UrlHelperPlugin;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Form\Form;

/**
 * Class PageController
 * @package Frontend\App\Controller
 *
 * @method UrlHelperPlugin|UriInterface url(string $route = null, array $params = [])
 * @method FlashMessengerPlugin messenger()
 * @method FormsPlugin|Form forms(string $name = null)
 * @method TemplatePlugin|string template(string $template = null, array $params = [])
 * @method AuthenticationPlugin authentication()
 * @method AuthorizationPlugin isGranted(string $permission, array $roles = [], mixed $context = null)
 *
 * @Service
 */
class PageController extends AbstractActionController
{
    /**
     * @return ResponseInterface
     */
    public function indexAction(): ResponseInterface
    {
        $data = [];

        $data['routerName'] = 'FastRoute';
        $data['routerDocs'] = 'https://github.com/nikic/FastRoute';

        $data['templateName'] = 'Twig';
        $data['templateDocs'] = 'http://twig.sensiolabs.org/documentation';

        return new HtmlResponse($this->template('app::home', $data));
    }

    /**
     * @return ResponseInterface
     */
    public function aboutUsAction(): ResponseInterface
    {
        return new HtmlResponse($this->template('page::about-us'));
    }

    /**
     * @return ResponseInterface
     */
    public function whoWeAreAction(): ResponseInterface
    {
        return new HtmlResponse($this->template('page::who-we-are'));
    }

    /**
     * @return ResponseInterface
     */
    public function premiumContentAction(): ResponseInterface
    {
        return new HtmlResponse($this->template('page::premium-content'));
    }
}
