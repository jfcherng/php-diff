<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Utility;

use InvalidArgumentException;
use Jfcherng\Diff\Renderer\AbstractRenderer;
use Jfcherng\Diff\Renderer\RendererConstant;

final class RendererFactory
{
    /**
     * Instances of renderers.
     *
     * @var AbstractRenderer[]
     */
    private static $singletons = [];

    /**
     * The constructor.
     */
    private function __construct()
    {
    }

    /**
     * Get a singleton of a template.
     *
     * @param string $template the template
     *
     * @return AbstractRenderer
     */
    public static function getInstance(string $template): AbstractRenderer
    {
        if (!isset(static::$singletons[$template])) {
            static::$singletons[$template] = static::make($template);
        }

        return static::$singletons[$template];
    }

    /**
     * Make a new instance of a template.
     *
     * @param string $template        the template
     * @param array  $templateOptions the template options
     *
     * @throws InvalidArgumentException
     *
     * @return AbstractRenderer
     */
    public static function make(string $template, array $templateOptions = []): AbstractRenderer
    {
        $className = static::resolveTemplate($template);

        if (!isset($className)) {
            throw new InvalidArgumentException("Template not found: {$template}");
        }

        return new $className($templateOptions);
    }

    /**
     * Resolve the template name into a FQCN.
     *
     * @param string $template The template
     *
     * @return null|string
     */
    public static function resolveTemplate(string $template): ?string
    {
        static $cache = [];

        // the result could be null so do not use isset() here
        if (\array_key_exists($template, $cache)) {
            return $cache[$template];
        }

        $result = null;

        foreach (RendererConstant::TEMPLATE_TYPES as $type) {
            $className = RendererConstant::RENDERER_NAMESPACE . "\\{$type}\\{$template}";

            if (\class_exists($className)) {
                $result = $className;

                break;
            }
        }

        return $cache[$template] = $result;
    }
}
