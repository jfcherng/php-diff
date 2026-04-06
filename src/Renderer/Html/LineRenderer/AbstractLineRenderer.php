<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html\LineRenderer;

use Jfcherng\Diff\Options\DifferOptions;
use Jfcherng\Diff\Options\RendererOptions;
use Jfcherng\Diff\SequenceMatcher;

/**
 * Base renderer for rendering HTML-based line diffs.
 */
abstract class AbstractLineRenderer implements LineRendererInterface
{
    protected SequenceMatcher $sequenceMatcher;

    protected DifferOptions $differOptions;

    protected RendererOptions $rendererOptions;

    /**
     * The constructor.
     *
     * @param DifferOptions   $differOptions   the differ options
     * @param RendererOptions $rendererOptions the renderer options
     */
    public function __construct(DifferOptions $differOptions, RendererOptions $rendererOptions)
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
     * @param DifferOptions $differOptions the differ options
     *
     * @return static
     */
    public function setDifferOptions(DifferOptions $differOptions): self
    {
        $this->differOptions = $differOptions;
        $this->sequenceMatcher->setOptions($differOptions->toArray());

        return $this;
    }

    /**
     * Set the renderer options.
     *
     * @param RendererOptions $rendererOptions the renderer options
     *
     * @return static
     */
    public function setRendererOptions(RendererOptions $rendererOptions): self
    {
        $this->rendererOptions = $rendererOptions;

        return $this;
    }

    /**
     * Gets the differ options.
     *
     * @return DifferOptions the differ options
     */
    public function getDifferOptions(): DifferOptions
    {
        return $this->differOptions;
    }

    /**
     * Gets the renderer options.
     *
     * @return RendererOptions the renderer options
     */
    public function getRendererOptions(): RendererOptions
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
