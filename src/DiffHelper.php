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
     * Get the absolute path of the project root directory.
     *
     * @return string
     */
    public static function getProjectDirectory(): string
    {
        static $path;

        return $path = $path ?? \realpath(__DIR__ . '/..');
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

        $glob = \implode(\DIRECTORY_SEPARATOR, [
            static::getProjectDirectory(),
            'src',
            'Renderer',
            '{' . \implode(',', RendererConstant::TEMPLATE_TYPES) . '}',
            '*.php',
        ]);

        $fileNames = \array_map(
            // get basename without file extension
            function (string $file): string {
                return \pathinfo($file, \PATHINFO_FILENAME);
            },
            // paths of all Renderer files
            \glob($glob, \GLOB_BRACE)
        );

        $templates = \array_filter(
            $fileNames,
            // only normal class files are wanted
            function (string $fileName): bool {
                return
                    \substr($fileName, 0, 8) !== 'Abstract' &&
                    \substr($fileName, -9) !== 'Interface' &&
                    \substr($fileName, -5) !== 'Trait';
            }
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
        static $fileContent;

        if (isset($fileContent)) {
            return $fileContent;
        }

        $filePath = static::getProjectDirectory() . '/example/diff-table.css';

        $file = new \SplFileObject($filePath, 'r');

        return $fileContent = $file->fread($file->getSize());
    }

    /**
     * All-in-one static method to calculate the diff between two strings (or arrays of strings).
     *
     * @param string|string[] $old             the old string (or array of lines)
     * @param string|string[] $new             the new string (or array of lines)
     * @param string          $template        the template name
     * @param array           $differOptions   the options for Differ object
     * @param array           $templateOptions the options for template object
     *
     * @return string the rendered differences
     */
    public static function calculate(
        $old,
        $new,
        string $template = 'Unified',
        array $differOptions = [],
        array $templateOptions = []
    ): string {
        // always convert into array form
        \is_string($old) && ($old = \explode("\n", $old));
        \is_string($new) && ($new = \explode("\n", $new));

        return RendererFactory::getInstance($template)
            ->setOptions($templateOptions)
            ->render(
                Differ::getInstance()
                    ->setOldNew($old, $new)
                    ->setOptions($differOptions)
            );
    }

    /**
     * All-in-one static method to calculate the diff between two files.
     *
     * @param string $old             the path of the old file
     * @param string $new             the path of the new file
     * @param string $template        the template name
     * @param array  $differOptions   the options for Differ object
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
        array $differOptions = [],
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
            $differOptions,
            $templateOptions
        );
    }
}
