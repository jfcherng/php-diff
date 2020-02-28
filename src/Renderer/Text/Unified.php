<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Text;

use Jfcherng\Diff\Differ;
use Jfcherng\Diff\SequenceMatcher;

/**
 * Unified diff generator.
 *
 * @see https://en.wikipedia.org/wiki/Diff#Unified_format
 */
final class Unified extends AbstractText
{
    /**
     * {@inheritdoc}
     */
    const INFO = [
        'desc' => 'Unified',
        'type' => 'Text',
    ];

    /**
     * {@inheritdoc}
     */
    protected function renderWorker(Differ $differ): string
    {
        $ret = '';

        foreach ($differ->getGroupedOpcodes() as $hunk) {
            $lastItem = \count($hunk) - 1;

            $i1 = $hunk[0][1];
            $i2 = $hunk[$lastItem][2];
            $j1 = $hunk[0][3];
            $j2 = $hunk[$lastItem][4];

            if ($i1 === 0 && $i2 === 0) {
                $i1 = $i2 = -1; // trick
            }

            $ret .= $this->renderHunkHeader($i1 + 1, $i2 - $i1, $j1 + 1, $j2 - $j1);

            foreach ($hunk as [$op, $i1, $i2, $j1, $j2]) {
                if ($op === SequenceMatcher::OP_EQ) {
                    $ret .= $this->renderContext(' ', $differ->getNew($j1, $j2));

                    continue;
                }

                if ($op & (SequenceMatcher::OP_REP | SequenceMatcher::OP_DEL)) {
                    $ret .= $this->renderContext('-', $differ->getOld($i1, $i2));
                }

                if ($op & (SequenceMatcher::OP_REP | SequenceMatcher::OP_INS)) {
                    $ret .= $this->renderContext('+', $differ->getNew($j1, $j2));
                }
            }
        }

        return $ret;
    }

    /**
     * Render the hunk header.
     *
     * @param int $a1 the a1
     * @param int $a2 the a2
     * @param int $b1 the b1
     * @param int $b2 the b2
     */
    protected function renderHunkHeader(int $a1, int $a2, int $b1, int $b2): string
    {
        return "@@ -{$a1},{$a2} +{$b1},{$b2} @@\n";
    }

    /**
     * Render the context array with the symbol.
     *
     * @param string   $symbol  the symbol
     * @param string[] $context the context
     */
    protected function renderContext(string $symbol, array $context): string
    {
        return empty($context)
            ? ''
            : $symbol . \implode("\n{$symbol}", $context) . "\n";
    }
}
