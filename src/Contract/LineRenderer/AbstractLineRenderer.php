<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Contract\LineRenderer;

use Jfcherng\Diff\SequenceMatcher;

/**
 * Base line renderer for rendering HTML-based line diffs.
 */
abstract class AbstractLineRenderer implements LineRendererInterface
{
    protected SequenceMatcher $sequenceMatcher;
    protected array $differOptions = [];
    protected array $rendererOptions = [];

    /**
     * The constructor.
     *
     * @param array $differOptions   the differ options
     * @param array $rendererOptions the renderer options
     */
    public function __construct(array $differOptions, array $rendererOptions)
    {
        $this->sequenceMatcher = new SequenceMatcher([], []);

        $this
            ->setDifferOptions($differOptions)
            ->setRendererOptions($rendererOptions)
        ;
    }

    /**
     * Set the differ options.
     *
     * @param array $differOptions the differ options
     */
    public function setDifferOptions(array $differOptions): static
    {
        $this->differOptions = $differOptions;
        $this->sequenceMatcher->setOptions($differOptions);

        return $this;
    }

    /**
     * Set the renderer options.
     *
     * @param array $rendererOptions the renderer options
     */
    public function setRendererOptions(array $rendererOptions): static
    {
        $this->rendererOptions = $rendererOptions;

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
     * Gets the renderer options.
     *
     * @return array the renderer options
     */
    public function getRendererOptions(): array
    {
        return $this->rendererOptions;
    }

    /**
     * Get the changed extent segments.
     *
     * @param string[] $old the old array
     * @param string[] $new the new array
     *
     * @return int[][] the changed extent segments
     */
    protected function getChangedExtentSegments(array $old, array $new): array
    {
        return $this->sequenceMatcher->setSequences($old, $new)->getOpcodes();
    }
}
