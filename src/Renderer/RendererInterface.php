<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer;

use Jfcherng\Diff\Differ;

/**
 * Renderer Interface.
 */
interface RendererInterface
{
    /**
     * Get the renderer result when the old and the new are the same.
     */
    public function getResultForIdenticals(): string;

    /**
     * Render the differ and return the result.
     *
     * @param Differ $differ the Differ object to be rendered
     */
    public function render(Differ $differ): string;
}
