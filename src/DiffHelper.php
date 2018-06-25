<?php

declare(strict_types=1);

namespace Jfcherng\Diff;

use Jfcherng\Diff\Renderer\RendererConstant;
use Jfcherng\Diff\Utility\RendererFactory;

class DiffHelper
{
    /**
     * The Diff object.
     *
     * @var Diff
     */
    protected static $diff;

    /**
     * Information of all available templates.
     *
     * @var array
     */
    protected static $templatesInfo;

    /**
     * Get the information about available templates.
     *
     * @return array
     */
    public static function getTemplatesInfo(): array
    {
        if (!isset(static::$templatesInfo)) {
            $glob = implode(
                DIRECTORY_SEPARATOR,
                [
                    __DIR__,
                    'Renderer',
                    '{' . implode(',', RendererConstant::TEMPLATE_TYPES) . '}',
                    '*.php',
                ]
            );

            $files = array_filter(
                glob($glob, GLOB_BRACE),
                // not an abstact class
                function (string $file): bool {
                    return strpos($file, 'Abstract') === false;
                }
            );

            // class name = file name without the extension
            $templates = array_map(
                function (string $file): string {
                    return basename($file, '.php');
                },
                $files
            );

            $info = [];
            foreach ($templates as $template) {
                $info[$template] = RendererFactory::resolveTemplate($template)::INFO;
            }

            static::$templatesInfo = $info;
        }

        return static::$templatesInfo;
    }

    /**
     * Get the available templates.
     *
     * @return string[] the available templates
     */
    public static function getAvailableTemplates(): array
    {
        return array_keys(static::getTemplatesInfo());
    }

    /**
     * All-in-one static method to calculate the diff.
     *
     * @param string|string[] $old             the old string (or array of chars)
     * @param string|string[] $new             the new string (or array of chars)
     * @param string          $template        the template name
     * @param array           $diffOptions     the options for Diff object
     * @param array           $templateOptions the options for template object
     *
     * @return string the difference
     */
    public static function calculate($old, $new, string $template = 'Unified', array $diffOptions = [], array $templateOptions = []): string
    {
        // the "no difference" situation may happen frequently
        // let's save some calculation if possible
        if ($old === $new) {
            return RendererFactory::resolveTemplate($template)::IDENTICAL_RESULT;
        }

        static::$diff = static::$diff ?? new Diff([], []);

        return static::$diff
            ->setA(is_string($old) ? explode("\n", $old) : $old)
            ->setB(is_string($new) ? explode("\n", $new) : $new)
            ->setOptions($diffOptions)
            ->render(
                RendererFactory::getInstance($template)
                    ->setOptions($templateOptions)
            );
    }
}
