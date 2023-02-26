<?php

declare(strict_types=1);

namespace Jfcherng\Diff\LineRenderer;

use Jfcherng\Diff\Contract\LineRenderer\AbstractLineRenderer;
use Jfcherng\Diff\Contract\Renderer\RendererConstant;
use Jfcherng\Diff\SequenceMatcher;
use Jfcherng\Diff\Utility\ReverseIterator;
use Jfcherng\Utility\MbString;

final class Char extends AbstractLineRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(MbString $mbOld, MbString $mbNew): void
    {
        $hunk = $this->getChangedExtentSegments($mbOld->toArray(), $mbNew->toArray());

        // reversely iterate hunk
        foreach (ReverseIterator::fromArray($hunk) as [$op, $i1, $i2, $j1, $j2]) {
            if ($op & (SequenceMatcher::OP_REP | SequenceMatcher::OP_DEL)) {
                $mbOld->str_enclose_i(RendererConstant::HTML_CLOSURES, $i1, $i2 - $i1);
            }

            if ($op & (SequenceMatcher::OP_REP | SequenceMatcher::OP_INS)) {
                $mbNew->str_enclose_i(RendererConstant::HTML_CLOSURES, $j1, $j2 - $j1);
            }
        }
    }
}
