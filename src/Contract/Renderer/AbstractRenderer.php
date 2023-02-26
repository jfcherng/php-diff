<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Contract\Renderer;

use Jfcherng\Diff\Differ;
use Jfcherng\Diff\SequenceMatcher;
use Jfcherng\Diff\Utility\Language;

/**
 * Base class for diff renderers.
 */
abstract class AbstractRenderer implements RendererInterface
{
    /**
     * Information about this renderer.
     *
     * @var array
     */
    public const INFO = [
        'desc' => 'default_desc',
        'type' => 'default_type',
    ];

    /**
     * Array of the opcodes and their corresponding symbols.
     *
     * @var array<int,string>
     */
    public const SYMBOL_MAP = [
        SequenceMatcher::OP_DEL => '-',
        SequenceMatcher::OP_EQ => ' ',
        SequenceMatcher::OP_INS => '+',
        SequenceMatcher::OP_REP => '!',
    ];

    /**
     * The language translation object.
     */
    protected Language $t;

    /**
     * If the input "changes" have `<ins>...</ins>` or `<del>...</del>`,
     * which means they have been processed, then `false`. Otherwise, `true`.
     */
    protected bool $changesAreRaw = true;

    /**
     * Array of the default options that apply to this renderer.
     */
    protected static array $defaultOptions = [
        // how detailed the rendered HTML in-line diff is? (none, line, word, char)
        'detailLevel' => 'line',
        // renderer language: eng, cht, chs, jpn, ...
        // or an array which has the same keys with a language file
        // check the "Custom Language" section in the readme for more advanced usage
        'language' => 'eng',
        // show line numbers in HTML renderers
        'lineNumbers' => true,
        // show a separator between different diff hunks in HTML renderers
        'separateBlock' => true,
        // show the (table) header
        'showHeader' => true,
        // convert spaces/tabs into HTML codes like `<span class="ch sp"> </span>`
        // and the frontend is responsible for rendering them with CSS.
        // when using this, "spacesToNbsp" should be false and "tabSize" is not respected.
        'spaceToHtmlTag' => false,
        // the frontend HTML could use CSS "white-space: pre;" to visualize consecutive whitespaces
        // but if you want to visualize them in the backend with "&nbsp;", you can set this to true
        'spacesToNbsp' => false,
        // HTML renderer tab width (negative = do not convert into spaces)
        'tabSize' => 4,
        // this option is currently only for the Combined renderer.
        // it determines whether a replace-type block should be merged or not
        // depending on the content changed ratio, which values between 0 and 1.
        'mergeThreshold' => 0.8,
        // this option is currently only for the Unified and the Context renderers.
        // CliColorEnum::Auto = colorize the output if possible (default)
        // CliColorEnum::Enabled = force to colorize the output
        // CliColorEnum::Disabled = force not to colorize the output
        'cliColorization' => CliColorEnum::Auto,
        // this option is currently only for the Json renderer.
        // internally, ops are all int type but this is not good for human reading.
        // set this to "true" to convert them into string form before outputting.
        'outputOpAsString' => false,
        // this option is currently only for the Json renderer.
        // it controls how the output JSON is formatted.
        // see availabe options on https://www.php.net/manual/en/function.json-encode.php
        'jsonEncodeFlags' => \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE,
        // this option is currently effective when the "detailLevel" is "word"
        // characters listed in this array can be used to make diff segments into a whole
        // for example, making "<del>good</del>-<del>looking</del>" into "<del>good-looking</del>"
        // this should bring better readability but set this to empty array if you do not want it
        'wordGlues' => ['-', ' '],
        // change this value to a string as the returned diff if the two input strings are identical
        'resultForIdenticals' => null,
        // extra HTML classes added to the DOM of the diff container
        'wrapperClasses' => ['diff-wrapper'],
    ];

    /**
     * Array containing the user applied and merged default options for the renderer.
     */
    protected array $options = [];

    /**
     * The constructor. Instantiates the rendering engine and if options are passed,
     * sets the options for the renderer.
     *
     * @param array $options optionally, an array of the options for the renderer
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * Set the options of the renderer to those supplied in the passed in array.
     * Options are merged with the default to ensure that there aren't any missing
     * options.
     *
     * @param array $options the options
     */
    public function setOptions(array $options): static
    {
        $newOptions = $options + static::$defaultOptions;

        $this->updateLanguage(
            $this->options['language'] ?? '',
            $newOptions['language'],
        );

        $this->options = $newOptions;

        return $this;
    }

    /**
     * Get the options.
     *
     * @return array the options
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    final public function getResultForIdenticals(): string
    {
        $custom = $this->options['resultForIdenticals'];

        if (isset($custom) && !\is_string($custom)) {
            throw new \InvalidArgumentException('renderer option `resultForIdenticals` must be null or string.');
        }

        return $custom ?? $this->getResultForIdenticalsDefault();
    }

    /**
     * Get the renderer default result when the old and the new are the same.
     */
    abstract public function getResultForIdenticalsDefault(): string;

    /**
     * {@inheritdoc}
     */
    final public function render(Differ $differ): string
    {
        $this->changesAreRaw = true;
        // the "no difference" situation may happen frequently
        return $differ->getOldNewComparison() === 0
            ? $this->getResultForIdenticals()
            : $this->renderWorker($differ);
    }

    /**
     * {@inheritdoc}
     */
    final public function renderArray(array $differArray): string
    {
        $this->changesAreRaw = false;

        return $this->renderArrayWorker($differArray);
    }

    /**
     * The real worker for self::render().
     *
     * @param Differ $differ the differ object
     */
    abstract protected function renderWorker(Differ $differ): string;

    /**
     * The real worker for self::renderArray().
     *
     * @param array[][] $differArray the differ array
     */
    abstract protected function renderArrayWorker(array $differArray): string;

    /**
     * Update the Language object.
     *
     * @param string|string[] $old the old language
     * @param string|string[] $new the new language
     */
    protected function updateLanguage($old, $new): static
    {
        if (!isset($this->t) || $old !== $new) {
            $this->t = new Language($new);
        }

        return $this;
    }

    /**
     * A shorthand to do translation.
     *
     * @param string $text       The text
     * @param bool   $escapeHtml Escape the translated text for HTML?
     *
     * @return string the translated text
     */
    protected function _(string $text, bool $escapeHtml = true): string
    {
        $text = $this->t->translate($text);

        return $escapeHtml ? htmlspecialchars($text) : $text;
    }
}
