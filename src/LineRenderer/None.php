<?php

declare(strict_types=1);

namespace Jfcherng\Diff\LineRenderer;

use Jfcherng\Diff\Contract\LineRenderer\AbstractLineRenderer;
use Jfcherng\Utility\MbString;

final class None extends AbstractLineRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(MbString $mbOld, MbString $mbNew): void
    {
    }
}
