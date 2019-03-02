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
     * @var array the differ options
     */
    protected $differOptions = [];

    /**
     * @var array the template options
     */
    protected $templateOptions = [];

    /**
     * The constructor.
     *
     * @param array $differOptions   the differ options
     * @param array $templateOptions the template options
     */
    public function __construct(array $differOptions, array $templateOptions)
    {
        $this->sequenceMatcher = new SequenceMatcher([], []);

        $this
            ->setDifferOptions($differOptions)
            ->setTemplateOptions($templateOptions);
    }

    /**
     * Set the differ options.
     *
     * @param array $differOptions the differ options
     *
     * @return self
     */
    public function setDifferOptions(array $differOptions): self
    {
        $this->differOptions = $differOptions;
        $this->sequenceMatcher->setOptions($differOptions);

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
     * Gets the differ options.
     *
     * @return array the differ options
     */
    public function getDifferOptions(): array
    {
        return $this->differOptions;
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
