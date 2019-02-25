<?php

declare(strict_types=1);

namespace Jfcherng\Diff;

use Jfcherng\Diff\Factory\RendererFactory;
use Jfcherng\Diff\Renderer\RendererConstant;

final class DiffHelper
{
    /**
     * The constructor.
     */
    private function __construct()
    {
    }

    /**
     * Get the information about available templates.
     *
     * @return array
     */
    public static function getTemplatesInfo(): array
    {
        static $info;

        if (isset($info)) {
            return $info;
        }

        $glob = \implode(
            \DIRECTORY_SEPARATOR,
            [
                __DIR__,
                'Renderer',
                '{' . \implode(',', RendererConstant::TEMPLATE_TYPES) . '}',
                '*.php',
            ]
        );

        $files = \array_filter(
            \glob($glob, \GLOB_BRACE),
            // not an abstact class
            function (string $file): bool {
                return \substr($file, 0, 8) !== 'Abstract';
            }
        );

        // class name = file name without the extension
        $templates = \array_map(
            function (string $file): string {
                return \pathinfo($file, \PATHINFO_FILENAME);
            },
            $files
        );

        $info = [];
        foreach ($templates as $template) {
            $info[$template] = RendererFactory::resolveTemplate($template)::INFO;
        }

        return $info;
    }

    /**
     * Get the available templates.
     *
     * @return string[] the available templates
     */
    public static function getAvailableTemplates(): array
    {
        return \array_keys(self::getTemplatesInfo());
    }

    /**
     * Get the content of the CSS style sheet for HTML templates.
     *
     * @throws \LogicException   path is a directory
     * @throws \RuntimeException path cannot be opened
     *
     * @return string
     */
    public static function getStyleSheet(): string
    {
        static $filePath = __DIR__ . '/../example/diff-table.css';

        $cssFile = new \SplFileObject($filePath, 'r');

        return $cssFile->fread($cssFile->getSize());
    }

    /**
     * All-in-one static method to calculate the diff between two strings (or arrays of strings).
     *
     * @param string|string[] $old             the old string (or array of lines)
     * @param string|string[] $new             the new string (or array of lines)
     * @param string          $template        the template name
     * @param array           $diffOptions     the options for Diff object
     * @param array           $templateOptions the options for template object
     *
     * @return string the rendered differences
     */
    public static function calculate(
        $old,
        $new,
        string $template = 'Unified',
        array $diffOptions = [],
        array $templateOptions = []
    ): string {
        // always convert into array form
        \is_string($old) && ($old = \explode("\n", $old));
        \is_string($new) && ($new = \explode("\n", $new));

        return Diff::getInstance()
            ->setOldNew($old, $new)
            ->setOptions($diffOptions)
            ->render(RendererFactory::getInstance($template)->setOptions($templateOptions));
    }

    /**
     * All-in-one static method to calculate the diff between two files.
     *
     * @param string $old             the path of the old file
     * @param string $new             the path of the new file
     * @param string $template        the template name
     * @param array  $diffOptions     the options for Diff object
     * @param array  $templateOptions the options for template object
     *
     * @throws \LogicException   path is a directory
     * @throws \RuntimeException path cannot be opened
     *
     * @return string the rendered differences
     */
    public static function calculateFiles(
        string $old,
        string $new,
        string $template = 'Unified',
        array $diffOptions = [],
        array $templateOptions = []
    ): string {
        // we want to leave the line-ending problem to static::calculate()
        // so do not set SplFileObject::DROP_NEW_LINE flag
        // otherwise, we will lose \r if the line-ending is \r\n
        $oldFile = new \SplFileObject($old, 'r');
        $newFile = new \SplFileObject($new, 'r');

        return static::calculate(
            $oldFile->fread($oldFile->getSize()),
            $newFile->fread($newFile->getSize()),
            $template,
            $diffOptions,
            $templateOptions
        );
    }
}
