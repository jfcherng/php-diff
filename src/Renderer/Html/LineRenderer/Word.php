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
    public function render(MbString $mbOld, MbString $mbNew): LineRendererInterface
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

        $oldWords = $mbOld->toArraySplit("/([{$punctuationsRange}]++)/uS", -1, \PREG_SPLIT_DELIM_CAPTURE);
        $newWords = $mbNew->toArraySplit("/([{$punctuationsRange}]++)/uS", -1, \PREG_SPLIT_DELIM_CAPTURE);

        $opcodes = $this->getChangedExtentSegments($oldWords, $newWords);

        // reversely iterate opcodes
        foreach (ReverseIterator::fromArray($opcodes) as [$tag, $i1, $i2, $j1, $j2]) {
            switch ($tag) {
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
