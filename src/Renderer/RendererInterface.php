<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer;

/**
 * Renderer Interface.
 */
interface RendererInterface
{
    /**
     * Render and return diff.
     *
     * @return string
     */
    public function render(): string;
}
