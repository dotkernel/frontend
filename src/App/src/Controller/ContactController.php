<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\App\Controller;

use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Dot\Controller\AbstractActionController;
use Dot\Controller\Plugin\Authentication\AuthenticationPlugin;
use Dot\Controller\Plugin\Authorization\AuthorizationPlugin;
use Dot\Controller\Plugin\FlashMessenger\FlashMessengerPlugin;
use Dot\Controller\Plugin\Forms\FormsPlugin;
use Dot\Controller\Plugin\TemplatePlugin;
use Dot\Controller\Plugin\UrlHelperPlugin;
use Fig\Http\Message\RequestMethodInterface;
use Frontend\App\Service\UserMessageServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Form\Form;
use Zend\Session\Container;

/**
 * Class ContactController
 * @package Frontend\App\Controller
 *
 * @method UrlHelperPlugin|UriInterface url(string $route = null, array $params = [])
 * @method FlashMessengerPlugin messenger()
 * @method FormsPlugin|Form forms(string $name = null)
 * @method TemplatePlugin|string template(string $template = null, array $params = [])
 * @method AuthenticationPlugin authentication()
 * @method AuthorizationPlugin isGranted(string $permission, array $roles = [], mixed $context = null)
 * @method Container session(string $namespace)
 *
 * @Service
 */
class ContactController extends AbstractActionController
{
    /** @var  UserMessageServiceInterface */
    protected $userMessageService;

    /**
     * ContactController constructor.
     * @param UserMessageServiceInterface $userMessageService
     *
     * @Inject({UserMessageServiceInterface::class})
     */
    public function __construct(UserMessageServiceInterface $userMessageService)
    {
        $this->userMessageService = $userMessageService;
    }

    /**
     * @return ResponseInterface
     */
    public function indexAction(): ResponseInterface
    {
        $form = $this->forms('ContactForm');
        $request = $this->getRequest();

        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            $data = $request->getParsedBody();

            $form->setData($data);
            if ($form->isValid()) {
                $message = $form->getData();
                $result = $this->userMessageService->save($message);
                if ($result) {
                    return new RedirectResponse($this->url('contact', ['action' => 'thank-you']));
                } else {
                    $this->messenger()->addError('Error saving message. Please try again');
                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $this->messenger()->addError($this->forms()->getMessages($form));
                $this->forms()->saveState($form);
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse($this->template('app::contact', [
            'form' => $form
        ]));
    }

    /**
     * @return ResponseInterface
     */
    public function thankYouAction(): ResponseInterface
    {
        return new HtmlResponse($this->template('app::thank-you'));
    }
}
