<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Text;

use Jfcherng\Diff\SequenceMatcher;

/**
 * Context diff generator.
 *
 * @see https://en.wikipedia.org/wiki/Diff#Context_format
 */
final class Context extends AbstractText
{
    /**
     * {@inheritdoc}
     */
    const INFO = [
        'desc' => 'Context',
        'type' => 'Text',
    ];

    /**
     * @var array array of the different opcode tags and how they map to the context diff equivalent
     */
    const TAG_MAP = [
        SequenceMatcher::OP_DEL => '-',
        SequenceMatcher::OP_EQ => ' ',
        SequenceMatcher::OP_INS => '+',
        SequenceMatcher::OP_REP => '!',
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

            $ret .=
                "***************\n" .
                $this->renderBlockHeader('*', $i1, $i2) .
                $this->renderBlockOld($opcodes) .
                $this->renderBlockHeader('-', $j1, $j2) .
                $this->renderBlockNew($opcodes);
        }

        return $ret;
    }

    /**
     * Render the block header.
     *
     * @param string $delimiter the delimiter
     * @param int    $a1        the a1
     * @param int    $a2        the a2
     *
     * @return string
     */
    protected function renderBlockHeader(string $delimiter, int $a1, int $a2): string
    {
        return
            "{$delimiter}{$delimiter}{$delimiter} " .
            ($a2 - $a1 >= 2 ? ($a1 + 1) . ',' . $a2 : $a2) .
            " {$delimiter}{$delimiter}{$delimiter}{$delimiter}\n";
    }

    /**
     * Render the old block.
     *
     * @param array $opcodes the opcodes
     *
     * @return string
     */
    protected function renderBlockOld(array $opcodes): string
    {
        $ret = '';

        foreach ($opcodes as [$tag, $i1, $i2, $j1, $j2]) {
            if ($tag === SequenceMatcher::OP_INS) {
                continue;
            }

            $ret .= $this->renderContext(
                self::TAG_MAP[$tag],
                $this->diff->getOld($i1, $i2)
            );
        }

        return $ret;
    }

    /**
     * Render the new block.
     *
     * @param array $opcodes the opcodes
     *
     * @return string
     */
    protected function renderBlockNew(array $opcodes): string
    {
        $ret = '';

        foreach ($opcodes as [$tag, $i1, $i2, $j1, $j2]) {
            if ($tag === SequenceMatcher::OP_DEL) {
                continue;
            }

            $ret .= $this->renderContext(
                self::TAG_MAP[$tag],
                $this->diff->getNew($j1, $j2)
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
