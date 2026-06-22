<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer;

use Jfcherng\Diff\Differ;
use Jfcherng\Diff\Options\RendererOptions;
use Jfcherng\Diff\SequenceMatcher;
use Jfcherng\Diff\Utility\Language;

/**
 * Base class for diff renderers.
 */
abstract class AbstractRenderer implements RendererInterface
{
    /**
     * @var array information about this renderer
     */
    public const array INFO = [
        'desc' => 'default_desc',
        'type' => 'default_type',
    ];

    /**
     * @var bool Is this renderer pure text?
     */
    public const bool IS_TEXT_RENDERER = true;

    /**
     * @var string[] array of the opcodes and their corresponding symbols
     */
    public const array SYMBOL_MAP = [
        SequenceMatcher::OP_DEL => '-',
        SequenceMatcher::OP_EQ => ' ',
        SequenceMatcher::OP_INS => '+',
        SequenceMatcher::OP_REP => '!',
    ];

    /**
     * @var Language the language translation object
     */
    protected $t;

    /**
     * If the input "changes" have `<ins>...</ins>` or `<del>...</del>`,
     * which means they have been processed, then `false`. Otherwise, `true`.
     *
     * @var bool
     */
    protected $changesAreRaw = true;

    protected RendererOptions $options;

    /**
     * The constructor. Instantiates the rendering engine and if options are passed,
     * sets the options for the renderer.
     *
     * @param array|RendererOptions $options optionally, the options for the renderer
     */
    public function __construct(RendererOptions|array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * Set the options of the renderer.
     *
     * @param array|RendererOptions $options the options
     *
     * @return static
     */
    public function setOptions(RendererOptions|array $options): self
    {
        $newOptions = $options instanceof RendererOptions
            ? $options
            : RendererOptions::fromArray($options);

        $this->updateLanguage(
            isset($this->options) ? $this->options->language : '',
            $newOptions->language,
        );

        $this->options = $newOptions;

        return $this;
    }

    /**
     * Get the options.
     */
    public function getOptions(): RendererOptions
    {
        return $this->options;
    }

    /**
     * @final
     *
     * @todo mark this method with "final" in the next major release
     */
    public function getResultForIdenticals(): string
    {
        return $this->options->resultForIdenticals ?? $this->getResultForIdenticalsDefault();
    }

    /**
     * Get the renderer default result when the old and the new are the same.
     */
    abstract public function getResultForIdenticalsDefault(): string;

    final public function render(Differ $differ): string
    {
        $this->changesAreRaw = true;

        // the "no difference" situation may happen frequently
        return $differ->getOldNewAreSame() && !$differ->options->fullContextIfIdentical
            ? $this->getResultForIdenticals()
            : $this->renderWorker($differ);
    }

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
