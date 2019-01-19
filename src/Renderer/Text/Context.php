<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Text;

use Jfcherng\Diff\Utility\SequenceMatcher;

/**
 * Context diff generator.
 */
class Context extends AbstractText
{
    /**
     * {@inheritdoc}
     */
    const INFO = [
        'desc' => 'Context',
    ];

    /**
     * @var array array of the different opcode tags and how they map to the context diff equivalent
     */
    const TAG_MAP = [
        SequenceMatcher::OPCODE_DELETE => '-',
        SequenceMatcher::OPCODE_EQUAL => ' ',
        SequenceMatcher::OPCODE_INSERT => '+',
        SequenceMatcher::OPCODE_REPLACE => '!',
    ];

    /**
     * {@inheritdoc}
     */
    public function render(): string
    {
        $ret = '';

        foreach ($this->diff->getGroupedOpcodes() as $opcodes) {
            $lastItem = \count($opcodes) - 1;

            $i1 = $opcodes[0][1];
            $i2 = $opcodes[$lastItem][2];
            $j1 = $opcodes[0][3];
            $j2 = $opcodes[$lastItem][4];

            $ret .= "***************\n";

            if ($i2 - $i1 >= 2) {
                $ret .= '*** ' . ($opcodes[0][1] + 1) . ',' . $i2 . " ****\n";
            } else {
                $ret .= '*** ' . $i2 . " ****\n";
            }

            if ($j2 - $j1 >= 2) {
                $separator = '--- ' . ($j1 + 1) . ',' . $j2 . " ----\n";
            } else {
                $separator = '--- ' . $j2 . " ----\n";
            }

            $hasVisible = false;
            foreach ($opcodes as $opcode) {
                if (
                    $opcode[0] === SequenceMatcher::OPCODE_REPLACE ||
                    $opcode[0] === SequenceMatcher::OPCODE_DELETE
                ) {
                    $hasVisible = true;

                    break;
                }
            }

            if ($hasVisible) {
                foreach ($opcodes as [$tag, $i1, $i2, $j1, $j2]) {
                    if ($tag === SequenceMatcher::OPCODE_INSERT) {
                        continue;
                    }

                    $ret .= static::TAG_MAP[$tag] . ' ' . \implode("\n" . static::TAG_MAP[$tag] . ' ', $this->diff->getA($i1, $i2)) . "\n";
                }
            }

            $hasVisible = false;
            foreach ($opcodes as $opcode) {
                if (
                    $opcode[0] === SequenceMatcher::OPCODE_REPLACE ||
                    $opcode[0] === SequenceMatcher::OPCODE_INSERT
                ) {
                    $hasVisible = true;

                    break;
                }
            }

            $ret .= $separator;

            if ($hasVisible) {
                foreach ($opcodes as [$tag, $i1, $i2, $j1, $j2]) {
                    if ($tag === SequenceMatcher::OPCODE_DELETE) {
                        continue;
                    }

                    $ret .= static::TAG_MAP[$tag] . ' ' . \implode("\n" . static::TAG_MAP[$tag] . ' ', $this->diff->getB($j1, $j2)) . "\n";
                }
            }
        }

        return $ret;
    }
}
