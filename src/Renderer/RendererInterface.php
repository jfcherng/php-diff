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
     * Render the differ and return the result.
     *
     * @param Differ $differ the Differ object to be rendered
     *
     * @return string
     */
    public function render(Differ $differ): string;
}
