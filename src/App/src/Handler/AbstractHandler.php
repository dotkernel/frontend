<?php

declare(strict_types=1);

namespace Frontend\App\Handler;

use Frontend\User\Entity\UserInterface;
use Dot\AnnotatedServices\Annotation\Inject;
use Frontend\Plugin\Exception\RuntimeException;
use Frontend\Plugin\PluginInterface;
use Frontend\Plugin\PluginManager;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class LoginHandler
 * @package Frontend\User\Handler
 */
abstract class AbstractHandler implements RequestHandlerInterface
{
    /** @var UserInterface|null $identity */
    private $identity;

    /** @var ServerRequestInterface */
    protected $request;

    /** @var  PluginManager */
    protected $pluginManager;

    /** @var TemplateRendererInterface $template */
    protected $template;

    /**
     * @return UserInterface|null
     */
    protected function getIdentity(): ?UserInterface
    {
        return $this->identity;
    }

    /**
     * @param UserInterface|null $identity
     */
    private function setIdentity(?UserInterface $identity): void
    {
        $this->identity = $identity;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * @param ServerRequestInterface $request
     */
    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * @return TemplateRendererInterface
     */
    public function getTemplate(): TemplateRendererInterface
    {
        return $this->template;
    }

    /**
     * @param TemplateRendererInterface $template
     */
    public function setTemplate(TemplateRendererInterface $template): void
    {
        $this->template = $template;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->setIdentity($request->getAttribute(UserInterface::class));
        $this->setRequest($request);

        $action = strtolower(trim($this->request->getAttribute('action', 'index')));
        if (empty($action)) {
            $action = 'index';
        }

        $action = self::getMethodFromAction($action);

        return $this->$action();
    }

    /**
     * Transform an "action" token into a method name
     *
     * @param  string $action
     * @return string
     */
    public static function getMethodFromAction(string $action): string
    {
        $method = str_replace(['.', '-', '_'], ' ', $action);
        $method = ucwords($method);
        $method = str_replace(' ', '', $method);
        $method = lcfirst($method);
        $method .= 'Action';
        return $method;
    }

    /**
     * Method overloading: return/call plugins
     *
     * If the plugin is a functor, call it, passing the parameters provided.
     * Otherwise, return the plugin instance.
     *
     * @param  string $method
     * @param  array $params
     * @return mixed
     */
    public function __call(string $method, array $params)
    {
        // TODO something
        // leave this here for non existing action
        return new HtmlResponse($this->template->render('error::404.html.twig'));

        $plugin = $this->plugin($method);
        if (is_callable($plugin)) {
            return call_user_func_array($plugin, $params);
        }
        return $plugin;
    }

    /**
     * Get plugin instance
     *
     * @param  string $name Name of plugin to return
     * @param  array $options Options to pass to plugin constructor (if not already instantiated)
     * @return PluginInterface|callable
     */
    public function plugin(string $name, array $options = []): PluginInterface
    {
        return $this->getPluginManager()->get($name, $options);
    }

    /**
     * @return PluginManager
     */
    public function getPluginManager(): PluginManager
    {
        var_dump($this->pluginManager); exit;
        if (!$this->pluginManager) {
            throw new RuntimeException(
                sprintf(
                    'Controller plugin manager not set for controller `%s`.' .
                    ' Enable the controller module by merging' .
                    ' its ConfigProvider and make sure the controller is registered in the service manager',
                    get_class($this)
                )
            );
        }

        return $this->pluginManager;
    }

    /**
     * @param PluginManager $pluginManager
     */
    public function setPluginManager(PluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }
}
