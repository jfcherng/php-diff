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

            $separatorFrom =
                '*** ' .
                ($i2 - $i1 >= 2 ? ($i1 + 1) . ',' . $i2 : $i2) .
                " ****\n";

            $separatorTo =
                '--- ' .
                ($j2 - $j1 >= 2 ? ($j1 + 1) . ',' . $j2 : $j2) .
                " ----\n";

            $ret .=
                "***************\n" .
                $separatorFrom .
                $this->renderBlockFrom($opcodes) .
                $separatorTo .
                $this->renderBlockTo($opcodes);
        }

        return $ret;
    }

    /**
     * Render the block: from.
     *
     * @param array $opcodes the opcodes
     *
     * @return string
     */
    protected function renderBlockFrom(array $opcodes): string
    {
        $ret = '';

        foreach ($opcodes as [$tag, $i1, $i2, $j1, $j2]) {
            if ($tag === SequenceMatcher::OPCODE_INSERT) {
                continue;
            }

            $ret .= $this->renderContext(
                self::TAG_MAP[$tag],
                $this->diff->getA($i1, $i2)
            );
        }

        return $ret;
    }

    /**
     * Render the block: to.
     *
     * @param array $opcodes the opcodes
     *
     * @return string
     */
    protected function renderBlockTo(array $opcodes): string
    {
        $ret = '';

        foreach ($opcodes as [$tag, $i1, $i2, $j1, $j2]) {
            if ($tag === SequenceMatcher::OPCODE_DELETE) {
                continue;
            }

            $ret .= $this->renderContext(
                self::TAG_MAP[$tag],
                $this->diff->getB($j1, $j2)
            );
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
            : "{$symbol} " . \implode("\n{$symbol} ", $context) . "\n";
    }
}
