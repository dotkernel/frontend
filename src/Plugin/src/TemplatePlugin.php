<?php

declare(strict_types=1);

namespace Frontend\Plugin;

use Mezzio\Template\TemplateRendererInterface;

/**
 * Class TemplatePlugin
 * @package Frontend\Plugin
 */
final class TemplatePlugin implements PluginInterface
{
    private readonly TemplateRendererInterface $templateRenderer;

    /**
     * TemplatePlugin constructor.
     */
    public function __construct(TemplateRendererInterface $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }

    /**
     * @param string|null $templateName
     */
    public function __invoke(string $templateName = null, array $params = []): TemplatePlugin|string
    {
        $args = func_get_args();
        if ($args === []) {
            return $this;
        }

        return $this->render($templateName, $params);
    }

    public function render(string $templateName, array $params = []): string
    {
        return $this->templateRenderer->render($templateName, $params);
    }

    public function addDefaultParam(string $templateName, string $param, mixed $value): void
    {
        $this->templateRenderer->addDefaultParam($templateName, $param, $value);
    }
}
