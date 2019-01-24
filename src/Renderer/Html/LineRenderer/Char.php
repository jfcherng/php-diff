<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html\LineRenderer;

use Jfcherng\Diff\Utility\ReverseIterator;
use Jfcherng\Diff\Utility\SequenceMatcher;
use Jfcherng\Utility\MbString;

class Char extends AbstractLineRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(MbString $mbFrom, MbString $mbTo): LineRendererInterface
    {
        $opcodes = $this->getChangeExtentSegments($mbFrom->toArray(), $mbTo->toArray());

        // reversely iterate opcodes
        foreach (ReverseIterator::fromArray($opcodes) as [$tag, $i1, $i2, $j1, $j2]) {
            switch ($tag) {
                case SequenceMatcher::OPCODE_DELETE:
                    $mbFrom->str_enclose_i(self::HTML_CLOSURES, $i1, $i2 - $i1);
                    break;
                case SequenceMatcher::OPCODE_INSERT:
                    $mbTo->str_enclose_i(self::HTML_CLOSURES, $j1, $j2 - $j1);
                    break;
                case SequenceMatcher::OPCODE_REPLACE:
                    $mbFrom->str_enclose_i(self::HTML_CLOSURES, $i1, $i2 - $i1);
                    $mbTo->str_enclose_i(self::HTML_CLOSURES, $j1, $j2 - $j1);
                    break;
                default:
                    continue 2;
            }
        }

        return $this;
    }
}
