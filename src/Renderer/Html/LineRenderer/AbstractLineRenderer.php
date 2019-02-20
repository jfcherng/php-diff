<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html\LineRenderer;

use Jfcherng\Diff\SequenceMatcher;

/**
 * Base renderer for rendering HTML-based line diffs.
 */
abstract class AbstractLineRenderer implements LineRendererInterface
{
    /**
     * @var SequenceMatcher the sequence matcher
     */
    protected $sequenceMatcher;

    /**
     * @var array the diff options
     */
    protected $diffOptions = [];

    /**
     * @var array the template options
     */
    protected $templateOptions = [];

    /**
     * The constructor.
     *
     * @param array $diffOptions     the difference options
     * @param array $templateOptions the template options
     */
    public function __construct(array $diffOptions, array $templateOptions)
    {
        $this->sequenceMatcher = new SequenceMatcher([], []);

        $this
            ->setDiffOptions($diffOptions)
            ->setTemplateOptions($templateOptions);
    }

    /**
     * Set the diff options.
     *
     * @param array $diffOptions the options
     *
     * @return self
     */
    public function setDiffOptions(array $diffOptions): self
    {
        $this->diffOptions = $diffOptions;
        $this->sequenceMatcher->setOptions($diffOptions);

        return $this;
    }

    /**
     * Set the template options.
     *
     * @param array $templateOptions the template options
     *
     * @return self
     */
    public function setTemplateOptions(array $templateOptions): self
    {
        $this->templateOptions = $templateOptions;

        return $this;
    }

    /**
     * Gets the diff options.
     *
     * @return array the diff options
     */
    public function getDiffOptions(): array
    {
        return $this->diffOptions;
    }

    /**
     * Gets the template options.
     *
     * @return array the template options
     */
    public function getTemplateOptions(): array
    {
        return $this->templateOptions;
    }

    /**
     * Get the changed extent segments.
     *
     * @param array $old the old array
     * @param array $new the new array
     *
     * @return array the changed extent segments
     */
    protected function getChangedExtentSegments(array $old, array $new): array
    {
        return $this->sequenceMatcher->setSequences($old, $new)->getOpcodes();
    }
}
