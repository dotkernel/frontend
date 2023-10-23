<?php

declare(strict_types=1);

namespace Frontend\Plugin;

use Mezzio\Template\TemplateRendererInterface;

use function func_get_args;

class TemplatePlugin implements PluginInterface
{
    public function __construct(protected TemplateRendererInterface $template)
    {
    }

    public function __invoke(?string $templateName = null, array $params = []): TemplatePlugin|string
    {
        $args = func_get_args();
        if (empty($args)) {
            return $this;
        }

        return $this->render($templateName, $params);
    }

    public function render(string $templateName, array $params = []): string
    {
        return $this->template->render($templateName, $params);
    }

    public function addDefaultParam(string $templateName, string $param, mixed $value): void
    {
        $this->template->addDefaultParam($templateName, $param, $value);
    }
}
