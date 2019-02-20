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
     */
    public function render(MbString $mbFrom, MbString $mbTo): LineRendererInterface
    {
        static $punctuationsRange = (
            // Latin-1 Supplement
            // @see https://unicode-table.com/en/blocks/latin-1-supplement/
            "\u{0080}-\u{00BB}" .
            // Spacing Modifier Letters
            // @see https://unicode-table.com/en/blocks/spacing-modifier-letters/
            "\u{02B0}-\u{02FF}" .
            // Combining Diacritical Marks
            // @see https://unicode-table.com/en/blocks/combining-diacritical-marks/
            "\u{0300}-\u{036F}" .
            // Small Form Variants
            // @see https://unicode-table.com/en/blocks/small-form-variants/
            "\u{FE50}-\u{FE6F}" .
            // General Punctuation
            // @see https://unicode-table.com/en/blocks/general-punctuation/
            "\u{2000}-\u{206F}" .
            // Supplemental Punctuation
            // @see https://unicode-table.com/en/blocks/supplemental-punctuation/
            "\u{2E00}-\u{2E7F}" .
            // CJK Symbols and Punctuation
            // @see https://unicode-table.com/en/blocks/cjk-symbols-and-punctuation/
            "\u{3000}-\u{303F}" .
            // Ideographic Symbols and Punctuation
            // @see https://unicode-table.com/en/blocks/ideographic-symbols-and-punctuation/
            "\u{16FE0}-\u{16FFF}" .
            // hmm... seems to be no rule
            " \t$,.:;!?'\"()\[\]{}%@<=>_+\-*\/~\\\\|" .
            '　＄，．：；！？’＂（）［］｛｝％＠＜＝＞＿＋－＊／～＼｜' .
            '「」『』〈〉《》【】()（）‘’“”' .
            '．‧・･•·¿'
        );

        $fromWords = $mbFrom->toArraySplit("/([{$punctuationsRange}]++)/uS", -1, \PREG_SPLIT_DELIM_CAPTURE);
        $toWords = $mbTo->toArraySplit("/([{$punctuationsRange}]++)/uS", -1, \PREG_SPLIT_DELIM_CAPTURE);

        $opcodes = $this->getChangedExtentSegments($fromWords, $toWords);

        // reversely iterate opcodes
        foreach (ReverseIterator::fromArray($opcodes) as [$tag, $i1, $i2, $j1, $j2]) {
            switch ($tag) {
                case SequenceMatcher::OP_DEL:
                    $fromWords[$i1] = RendererConstant::HTML_CLOSURES[0] . $fromWords[$i1];
                    $fromWords[$i2 - 1] .= RendererConstant::HTML_CLOSURES[1];
                    break;
                case SequenceMatcher::OP_INS:
                    $toWords[$j1] = RendererConstant::HTML_CLOSURES[0] . $toWords[$j1];
                    $toWords[$j2 - 1] .= RendererConstant::HTML_CLOSURES[1];
                    break;
                case SequenceMatcher::OP_REP:
                    $fromWords[$i1] = RendererConstant::HTML_CLOSURES[0] . $fromWords[$i1];
                    $fromWords[$i2 - 1] .= RendererConstant::HTML_CLOSURES[1];
                    $toWords[$j1] = RendererConstant::HTML_CLOSURES[0] . $toWords[$j1];
                    $toWords[$j2 - 1] .= RendererConstant::HTML_CLOSURES[1];
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
