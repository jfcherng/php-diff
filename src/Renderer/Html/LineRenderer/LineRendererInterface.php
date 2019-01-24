<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html\LineRenderer;

use Jfcherng\Utility\MbString;

interface LineRendererInterface
{
    /**
     * Renderer the in-line changed extent.
     *
     * @param MbString $mbFrom the megabytes from line
     * @param MbString $mbTo   the megabytes to line
     *
     * @return self
     */
    public function render(MbString $mbFrom, MbString $mbTo): self;
}
