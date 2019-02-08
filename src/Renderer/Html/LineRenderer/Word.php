<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html\LineRenderer;

use Jfcherng\Diff\SequenceMatcher;
use Jfcherng\Diff\Utility\ReverseIterator;
use Jfcherng\Utility\MbString;

final class Word extends AbstractLineRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(MbString $mbFrom, MbString $mbTo): LineRendererInterface
    {
        static $punctuations = (
            ' $,.:;!?\'"()\[\]{}%@<=>_+\-*\/~\\\\|' .
            '　＄，．：；！？’＂（）［］｛｝％＠＜＝＞＿＋－＊／～＼｜' .
            '「」『』〈〉《》【】()）（‘’“”' .
            '．‧・･•·'
        );

        $fromWords = $mbFrom->toArraySplit("/([{$punctuations}])/uS", -1, \PREG_SPLIT_DELIM_CAPTURE);
        $toWords = $mbTo->toArraySplit("/([{$punctuations}])/uS", -1, \PREG_SPLIT_DELIM_CAPTURE);

        $opcodes = $this->getChangedExtentSegments($fromWords, $toWords);

        // reversely iterate opcodes
        foreach (ReverseIterator::fromArray($opcodes) as [$tag, $i1, $i2, $j1, $j2]) {
            switch ($tag) {
                case SequenceMatcher::OPCODE_DELETE:
                    $fromWords[$i1] = self::HTML_CLOSURES[0] . $fromWords[$i1];
                    $fromWords[$i2 - 1] .= self::HTML_CLOSURES[1];
                    break;
                case SequenceMatcher::OPCODE_INSERT:
                    $toWords[$j1] = self::HTML_CLOSURES[0] . $toWords[$j1];
                    $toWords[$j2 - 1] .= self::HTML_CLOSURES[1];
                    break;
                case SequenceMatcher::OPCODE_REPLACE:
                    $fromWords[$i1] = self::HTML_CLOSURES[0] . $fromWords[$i1];
                    $fromWords[$i2 - 1] .= self::HTML_CLOSURES[1];
                    $toWords[$j1] = self::HTML_CLOSURES[0] . $toWords[$j1];
                    $toWords[$j2 - 1] .= self::HTML_CLOSURES[1];
                    break;
                default:
                    continue 2;
            }
        }

        $mbFrom->set(\implode('', $fromWords));
        $mbTo->set(\implode('', $toWords));

        return $this;
    }
}
