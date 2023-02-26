<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Contract\LineRenderer;

use Jfcherng\Utility\MbString;

interface LineRendererInterface
{
    /**
     * Render the inline changed extent.
     *
     * @param MbString $mbOld the old megabytes line
     * @param MbString $mbNew the new megabytes line
     */
    public function render(MbString $mbOld, MbString $mbNew): void;
}
