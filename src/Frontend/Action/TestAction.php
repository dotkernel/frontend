<?php
/**
 * @copyright: DotKernel
 * @library: dot-frontend
 * @author: n3vrax
 * Date: 1/21/2017
 * Time: 4:19 AM
 */

namespace Dot\Frontend\Action;

use Dot\AnnotatedServiced\Annotation\Inject;
use Dot\AnnotatedServiced\Annotation\Service;
use Dot\Mail\Service\MailServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Class TestAction
 * @package Dot\Frontend\Action
 * @Service("TestAction")
 */
class TestAction
{
    /** @var  UrlHelper */
    protected $urlHelper;

    /** @var  bool */
    protected $debug;

    /** @var  MailServiceInterface */
    protected $mailService;

    /**
     * TestAction constructor.
     * @param UrlHelper $urlHelper
     * @param MailServiceInterface $mailService
     * @param bool $debug
     *
     * @Inject({UrlHelper::class, "dot-mail.service.default", "config.debug"})
     */
    public function __construct(
        UrlHelper $urlHelper,
        MailServiceInterface $mailService,
        $debug = false
    ) {
        $this->urlHelper = $urlHelper;
        $this->mailService = $mailService;
        $this->debug = $debug;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        var_dump(get_class($this->urlHelper));
        var_dump(get_class($this->mailService));
        var_dump($this->debug);
        exit;
    }
}
