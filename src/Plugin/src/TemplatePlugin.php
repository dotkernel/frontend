<?php

declare(strict_types=1);

namespace Frontend\Plugin;

use Mezzio\Template\TemplateRendererInterface;

/**
 * Class TemplatePlugin
 * @package Frontend\Plugin
 */
class TemplatePlugin implements PluginInterface
{
    protected TemplateRendererInterface $template;

    /**
     * TemplatePlugin constructor.
     * @param TemplateRendererInterface $template
     */
    public function __construct(TemplateRendererInterface $template)
    {
        $this->template = $template;
    }

    /**
     * @param string|null $templateName
     * @param array $params
     * @return TemplatePlugin|string
     */
    public function __invoke(string $templateName = null, array $params = []): TemplatePlugin|string
    {
        $args = func_get_args();
        if (empty($args)) {
            return $this;
        }

        return $this->render($templateName, $params);
    }

    /**
     * @param string $templateName
     * @param array $params
     * @return string
     */
    public function render(string $templateName, array $params = []): string
    {
        return $this->template->render($templateName, $params);
    }

    /**
     * @param string $templateName
     * @param string $param
     * @param mixed $value
     */
    public function addDefaultParam(string $templateName, string $param, mixed $value): void
    {
        $this->template->addDefaultParam($templateName, $param, $value);
    }
}
