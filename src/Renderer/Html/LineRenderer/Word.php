<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html\LineRenderer;

use Jfcherng\Diff\Renderer\RendererConstant;
use Jfcherng\Diff\SequenceMatcher;
use Jfcherng\Diff\Utility\ReverseIterator;
use Jfcherng\Utility\MbString;

final class Word extends AbstractLineRenderer
{
    /**
     * {@inheritdoc}
     *
     * @return static
     */
    public function render(MbString $mbOld, MbString $mbNew): LineRendererInterface
    {
        static $splitRegex = '/([' . RendererConstant::PUNCTUATIONS_RANGE . ']++)/uS';

        $oldWords = $mbOld->toArraySplit($splitRegex, -1, \PREG_SPLIT_DELIM_CAPTURE);
        $newWords = $mbNew->toArraySplit($splitRegex, -1, \PREG_SPLIT_DELIM_CAPTURE);

        $hunk = $this->getChangedExtentSegments($oldWords, $newWords);

        // reversely iterate hunk
        foreach (ReverseIterator::fromArray($hunk) as [$op, $i1, $i2, $j1, $j2]) {
            switch ($op) {
                case SequenceMatcher::OP_DEL:
                    $oldWords[$i1] = RendererConstant::HTML_CLOSURES[0] . $oldWords[$i1];
                    $oldWords[$i2 - 1] .= RendererConstant::HTML_CLOSURES[1];

                    break;
                case SequenceMatcher::OP_INS:
                    $newWords[$j1] = RendererConstant::HTML_CLOSURES[0] . $newWords[$j1];
                    $newWords[$j2 - 1] .= RendererConstant::HTML_CLOSURES[1];

                    break;
                case SequenceMatcher::OP_REP:
                    $oldWords[$i1] = RendererConstant::HTML_CLOSURES[0] . $oldWords[$i1];
                    $oldWords[$i2 - 1] .= RendererConstant::HTML_CLOSURES[1];
                    $newWords[$j1] = RendererConstant::HTML_CLOSURES[0] . $newWords[$j1];
                    $newWords[$j2 - 1] .= RendererConstant::HTML_CLOSURES[1];

                    break;
                default:
                    continue 2;
            }
        }

        $mbOld->set(\implode('', $oldWords));
        $mbNew->set(\implode('', $newWords));

        return $this;
    }
}
