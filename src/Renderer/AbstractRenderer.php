<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer;

use Jfcherng\Diff\Differ;
use Jfcherng\Diff\Utility\Language;

/**
 * Base class for diff renderers.
 */
abstract class AbstractRenderer implements RendererInterface
{
    /**
     * @var array information about this renderer
     */
    const INFO = [
        'desc' => 'default_desc',
        'type' => 'default_type',
    ];

    /**
     * @var bool Is this renderer pure text?
     */
    const IS_TEXT_RENDERER = true;

    /**
     * @var Language the language translation object
     */
    protected $t;

    /**
     * @var array array of the default options that apply to this renderer
     */
    protected static $defaultOptions = [
        // how detailed the rendered HTML in-line diff is? (none, line, word, char)
        'detailLevel' => 'line',
        // renderer language: eng, cht, chs, jpn, ...
        // or an array which has the same keys with a language file
        'language' => 'eng',
        // HTML renderer tab width (negative = do not convert into spaces)
        'tabSize' => 4,
        // show a separator between different diff hunks in HTML renderers
        'separateBlock' => true,
        // the frontend HTML could use CSS "white-space: pre;" to visualize consecutive whitespaces
        // but if you want to visualize them in the backend with "&nbsp;", you can set this to true
        'spacesToNbsp' => false,
        // internally, ops (tags) are all int type but this is not good for human reading.
        // set this to "true" to convert them into string form before outputting.
        'outputTagAsString' => false,
        // change this value to a string as the returned diff if the two input strings are identical
        'resultForIdenticals' => null,
        // extra HTML classes added to the DOM of the diff container
        'wrapperClasses' => ['diff-wrapper'],
    ];

    /**
     * @var array array containing the user applied and merged default options for the renderer
     */
    protected $options = [];

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
     *
     * @return static
     */
    public function setOptions(array $options): self
    {
        $newOptions = $options + static::$defaultOptions;

        $this->updateLanguage(
            $this->options['language'] ?? '',
            $newOptions['language']
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
     * @final
     *
     * @todo mark this method with "final" in the next major release
     *
     * @throws \InvalidArgumentException
     */
    public function getResultForIdenticals(): string
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
        // the "no difference" situation may happen frequently
        return $differ->getOldNewComparison() === 0
            ? $this->getResultForIdenticals()
            : $this->renderWoker($differ);
    }

    /**
     * {@inheritdoc}
     */
    final public function renderArray(array $differArray): string
    {
        return $this->renderArrayWoker($differArray);
    }

    /**
     * The real worker for self::render().
     *
     * @param Differ $differ the differ object
     */
    abstract protected function renderWoker(Differ $differ): string;
    
    /**
     * The worker for array render.
     *
     * @param array $differArray the differ array
     *
     * @return string
     */
    abstract protected function renderArrayWoker(array $differArray): string;
	
	/**
     * Woker's base function.
     *
     * @param array $changes the changes array
     *
     * @return string
     */
    abstract protected function baseWoker(array $changes): string;

    /**
     * Update the Language object.
     *
     * @param string|string[] $old the old language
     * @param string|string[] $new the new language
     *
     * @return static
     */
    protected function updateLanguage($old, $new): self
    {
        if (!isset($this->t) || $old !== $new) {
            $this->t = new Language($new);
        }

        return $this;
    }

    /**
     * A shorthand to do translation.
     *
     * @param string $text         The text
     * @param array  $placeholders The placeholders
     * @param bool   $escapeHtml   Escape the translated text for HTML?
     *
     * @return string the translated text
     */
    protected function _(string $text, array $placeholders = [], bool $escapeHtml = true): string
    {
        $text = $this->t->translate($text, $placeholders);

        return $escapeHtml ? \htmlspecialchars($text) : $text;
    }
}
