<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Text;

use Jfcherng\Diff\Utility\SequenceMatcher;

/**
 * Unified diff generator.
 */
class Unified extends AbstractText
{
    /**
     * {@inheritdoc}
     */
    const INFO = [
        'desc' => 'Unified',
    ];

    /**
     * {@inheritdoc}
     */
    public function render(): string
    {
        $ret = '';

        foreach ($this->diff->getGroupedOpcodes() as $group) {
            $lastItem = \count($group) - 1;

            $i1 = $group[0][1];
            $i2 = $group[$lastItem][2];
            $j1 = $group[0][3];
            $j2 = $group[$lastItem][4];

            if ($i1 === 0 && $i2 === 0) {
                $i1 = $i2 = -1;
            }

            $ret .= '@@ -' . ($i1 + 1) . ',' . ($i2 - $i1) . ' +' . ($j1 + 1) . ',' . ($j2 - $j1) . " @@\n";

            foreach ($group as $opcode) {
                [$tag, $i1, $i2, $j1, $j2] = $opcode;

                if ($tag === SequenceMatcher::OPCODE_EQUAL) {
                    $ret .= ' ' . \implode("\n ", $this->diff->getA($i1, $i2)) . "\n";

                    continue;
                }

                if (
                    $tag === SequenceMatcher::OPCODE_REPLACE ||
                    $tag === SequenceMatcher::OPCODE_DELETE
                ) {
                    $ret .= '-' . \implode("\n-", $this->diff->getA($i1, $i2)) . "\n";
                }

                if (
                    $tag === SequenceMatcher::OPCODE_REPLACE ||
                    $tag === SequenceMatcher::OPCODE_INSERT
                ) {
                    $ret .= '+' . \implode("\n+", $this->diff->getB($j1, $j2)) . "\n";
                }
            }
        }

        return $ret;
    }
}
