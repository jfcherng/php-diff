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

        // var_dump($this->diff->getGroupedOpcodes());
        foreach ($this->diff->getGroupedOpcodes() as $opcodes) {
            $lastItem = \count($opcodes) - 1;

            $i1 = $opcodes[0][1];
            $i2 = $opcodes[$lastItem][2];
            $j1 = $opcodes[0][3];
            $j2 = $opcodes[$lastItem][4];

            if ($i1 === 0 && $i2 === 0) {
                $i1 = $i2 = -1;
            }

            $ret .= '@@ -' . ($i1 + 1) . ',' . ($i2 - $i1) . ' +' . ($j1 + 1) . ',' . ($j2 - $j1) . " @@\n";

            foreach ($opcodes as [$tag, $i1, $i2, $j1, $j2]) {
                if ($tag === SequenceMatcher::OPCODE_EQUAL) {
                    $ret .= $this->renderContext(' ', $this->diff->getA($i1, $i2));

                    continue;
                }

                if (
                    $tag === SequenceMatcher::OPCODE_REPLACE ||
                    $tag === SequenceMatcher::OPCODE_DELETE
                ) {
                    $ret .= $this->renderContext('-', $this->diff->getA($i1, $i2));
                }

                if (
                    $tag === SequenceMatcher::OPCODE_REPLACE ||
                    $tag === SequenceMatcher::OPCODE_INSERT
                ) {
                    $ret .= $this->renderContext('+', $this->diff->getB($j1, $j2));
                }
            }
        }

        return $ret;
    }

    /**
     * Render the context array with the symbol.
     *
     * @param string $symbol  the symbol
     * @param array  $context the context
     *
     * @return string
     */
    protected function renderContext(string $symbol, array $context): string
    {
        return empty($context)
            ? ''
            : $symbol . \implode("\n{$symbol}", $context) . "\n";
    }
}
