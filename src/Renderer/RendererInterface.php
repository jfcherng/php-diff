<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer;

use Jfcherng\Diff\Diff;

/**
 * Renderer Interface.
 */
interface RendererInterface
{
    /**
     * Render and return diff.
     *
     * @param Diff $diff the diff object to be rendered
     *
     * @return string
     */
    public function render(Diff $diff): string;
}
